<?php

namespace Coke\Sarp2\Plugin;

use Aheadworks\Sarp2\Api\Data\ProfileAddressInterface;
use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Aheadworks\Sarp2\Model\Config;
use Aheadworks\Sarp2\Model\Profile\Address\ToOrder;
use Coke\Sarp2\Helper\Config as CokeSarp2Config;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class ProfileAddressToOrderPlugin
{
    const FREE_SHIPPING = 'freeshipping_freeshipping';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CokeSarp2Config
     */
    private $cokeSarpConfg;
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * ProfileAddressToOrderPlugin constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     * @param CokeSarp2Config $cokeSarpConfg
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        CokeSarp2Config $cokeSarpConfg,
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->cokeSarpConfg = $cokeSarpConfg;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param ToOrder $subject
     * @param $result
     * @param ProfileAddressInterface $profileAddress
     * @param $paymentPeriod
     */
    public function afterConvert(
        ToOrder $subject,
        $result,
        ProfileAddressInterface $profileAddress,
        $paymentPeriod
    ) {
        /** @var OrderInterface $result */
        if (!$this->cokeSarpConfg->canSetShippingOnAddressToOrderConversion($result->getStoreId())) {
            return $result;
        }

        if (!($profile = $this->getProfileById($profileAddress->getProfileId()))) {
            return $result;
        }

        if (!$result->getIsVirtual() && !$result->getShippingMethod()) {
            $result->setData('shipping_method', $profile->getCheckoutShippingMethod());
            $result->setData('shipping_description', $profile->getCheckoutShippingDescription());
        }

        return $result;
    }

    /**
     * @param $profileId
     * @return ProfileInterface|null
     */
    private function getProfileById($profileId): ?ProfileInterface
    {
        try {
            return $this->profileRepository->get($profileId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->info(__('[ProfileAddressToOrderPlugin] %1', $e->getMessage()));
        }

        return null;
    }
}
