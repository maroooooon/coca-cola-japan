<?php

namespace Coke\Sarp2\Plugin;

use Aheadworks\Sarp2\Api\ProfileManagementInterface;
use Aheadworks\Sarp2\Model\Profile\Source\Status;
use Coke\Sarp2\Helper\Profile\Order as ProfileOrderHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class WhitelistCancelDeniedOrdersPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProfileOrderHelper
     */
    private $profileOrderHelper;
    /**
     * @var ProfileManagementInterface
     */
    private $profileManagement;

    /**
     * @param LoggerInterface $logger
     * @param ProfileOrderHelper $profileOrderHelper
     * @param ProfileManagementInterface $profileManagement
     */
    public function __construct(
        LoggerInterface $logger,
        ProfileOrderHelper $profileOrderHelper,
        ProfileManagementInterface $profileManagement
    ) {
        $this->logger = $logger;
        $this->profileOrderHelper = $profileOrderHelper;
        $this->profileManagement = $profileManagement;
    }

    /**
     * @param \Coke\Whitelist\Cron\CancelDeniedOrders $subject
     * @param $result
     * @param OrderInterface $order
     * @return mixed
     */
    public function afterCancelOrder(
        \Coke\Whitelist\Cron\CancelDeniedOrders $subject,
        $result,
        OrderInterface $order
    ) {
        if ($profile = $this->profileOrderHelper->getProfileFromOrder($order)) {
            try {
                $this->profileManagement->changeStatusAction($profile->getProfileId(), Status::CANCELLED);
            } catch (LocalizedException $e) {
                $this->logger->info(__('[WhitelistCancelDeniedOrdersPlugin] Error: %1', $e->getMessage()));
            }
        }

        return $result;
    }
}
