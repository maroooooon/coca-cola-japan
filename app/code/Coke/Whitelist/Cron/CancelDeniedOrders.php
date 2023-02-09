<?php

namespace Coke\Whitelist\Cron;

use Coke\Whitelist\Model\Aggregator\DeniedOrdersAggregator;
use Coke\Whitelist\Model\ModuleConfig;
use Coke\WhitelistEmail\Model\Email\Sender\OrderDeniedSender;
use Magento\Framework\App\Area;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;
use StripeIntegration\Payments\Model\Config;

class CancelDeniedOrders
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $stripeConfig;
    /**
     * @var DeniedOrdersAggregator
     */
    private $deniedOrdersAggregator;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;
    /**
     * @var OrderDeniedSender
     */
    private $orderDeniedSender;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @param LoggerInterface $logger
     * @param Config $stripeConfig
     * @param DeniedOrdersAggregator $deniedOrdersAggregator
     * @param Emulation $emulation
     * @param OrderManagementInterface $orderManagement
     * @param OrderDeniedSender $orderDeniedSender
     * @param OrderRepositoryInterface $orderRepository
     * @param ModuleConfig $config
     */
    public function __construct(
        LoggerInterface $logger,
        Config $stripeConfig,
        DeniedOrdersAggregator $deniedOrdersAggregator,
        Emulation $emulation,
        OrderManagementInterface $orderManagement,
        OrderDeniedSender $orderDeniedSender,
        OrderRepositoryInterface $orderRepository,
        ModuleConfig $config
    ) {
        $this->logger = $logger;
        $this->stripeConfig = $stripeConfig;
        $this->deniedOrdersAggregator = $deniedOrdersAggregator;
        $this->emulation = $emulation;
        $this->orderManagement = $orderManagement;
        $this->orderDeniedSender = $orderDeniedSender;
        $this->orderRepository = $orderRepository;
        $this->config = $config;
    }

    /**
     * @param OrderInterface $order
     */
    private function resetStripeConfiguration(OrderInterface $order)
    {
        $paymentMethodCode = $order->getPayment()->getMethod();
        if (strpos($paymentMethodCode, "stripe") === -1) {
            // ignore because this order was not placed with stripe.
            return;
        }

        $this->stripeConfig->reInitStripe($order->getStoreId(), $order->getOrderCurrencyCode(), null);
    }

    /**
     * Cancel denied orders
     */
    public function execute()
    {
        $enabledStoreIds = $this->deniedOrdersAggregator->getEnabledStoreIds();

        foreach ($enabledStoreIds as $storeId) {
            if ($this->config->isCancelDeniedOrderEnabled($storeId)) {
                $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

                foreach ($this->deniedOrdersAggregator->getOrders($storeId) as $order) {
                    $this->resetStripeConfiguration($order);
                    $this->cancelOrder($order);
                }

                $this->emulation->stopEnvironmentEmulation();
            }
        }
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function cancelOrder(OrderInterface $order): bool
    {
        try {
            if ($order->canCancel()) {
                $this->orderManagement->cancel($order->getEntityId());
                $order->addCommentToStatusHistory(__('[Whitelist] Order was canceled.'));
                $this->sendOrderDeniedEmail($order);
                return true;
            }
        } catch (\Magento\Framework\Exception\LocalizedException | \Exception $e) {
            $order->addCommentToStatusHistory(__('[Whitelist] Order could not be canceled.'));
            $this->logger->info(__('[CancelDeniedOrders] Error: %1', $e->getMessage()));
        }

        return false;
    }

    /**
     * @param OrderInterface $order
     */
    private function sendOrderDeniedEmail(OrderInterface $order)
    {
        if ($this->orderDeniedSender->send($order)) {
            $order->addCommentToStatusHistory(__('[Whitelist] Customer sent order denied email.'));
        } else {
            $order->addCommentToStatusHistory(__('[Whitelist] Customer was not sent order denied email.'));
        }
    }
}
