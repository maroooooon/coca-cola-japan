<?php

namespace Coke\Sarp2\Service;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Api\Data\ScheduledPaymentInfoInterface;
use Aheadworks\Sarp2\Api\ProfileManagementInterface;
use Aheadworks\Sarp2\Model\Profile\Source\Status as StatusSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class DeliveryDateCalculator
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var array
     */
    private $deliveryDate;
    /**
     * @var ProfileManagementInterface
     */
    private $profileManagement;

    /**
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param TimezoneInterface $timezone
     * @param ScopeConfigInterface $scopeConfig
     * @param ProfileManagementInterface $profileManagement
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        TimezoneInterface $timezone,
        ScopeConfigInterface $scopeConfig,
        ProfileManagementInterface $profileManagement
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->timezone = $timezone;
        $this->scopeConfig = $scopeConfig;
        $this->profileManagement = $profileManagement;
    }

    /**
     * @param int $profileId
     * @return string|null
     */
    public function getDeliveryDate(int $profileId): ?string
    {
        if ($deliveryDate = $this->calculateDeliveryDate($profileId)) {
            return $this->timezone->formatDate($deliveryDate, \IntlDateFormatter::MEDIUM);
        }

        return null;
    }

    /**
     * @param int $profileId
     * @return \DateTime|null
     */
    private function calculateDeliveryDate(int $profileId): ?\DateTime
    {
        try {
            if (isset($this->deliveryDate[$profileId])) {
                return $this->deliveryDate[$profileId];
            }

            $nextPaymentDate = $this->timezone->date($this->getNextPaymentDate($profileId));
            $processingDays = $this->getProcessingDays();

            $deliveryDate = $nextPaymentDate;
            while ($processingDays > 0) {
                $deliveryDate->add(new \DateInterval('P1D'));

                if (!$this->isWeekend($deliveryDate)) {
                    $processingDays--;
                }
            }

            $this->deliveryDate[$profileId] = $deliveryDate;
            return $this->deliveryDate[$profileId];
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @param null $store
     * @return int
     */
    private function getProcessingDays($store = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'aw_sarp2/delivery_date/processing_time',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    private function isWeekend(\DateTime $dateTime): bool
    {
        return in_array($dateTime->format('N'), [6, 7]);
    }

    /**
     * @param int $profileId
     * @return string|null
     * @throws LocalizedException
     */
    private function getNextPaymentDate(int $profileId): ?string
    {
        $nextPaymentInfo = $this->profileManagement->getNextPaymentInfo($profileId);
        $nextPaymentDate = $nextPaymentInfo->getPaymentDate();
        return $this->timezone->formatDate($nextPaymentDate, \IntlDateFormatter::MEDIUM);
    }
}
