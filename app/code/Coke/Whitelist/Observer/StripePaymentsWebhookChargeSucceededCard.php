<?php

namespace Coke\Whitelist\Observer;

use Coke\Whitelist\Model\ModuleConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use StripeIntegration\Payments\Helper\Webhooks;

class StripePaymentsWebhookChargeSucceededCard implements ObserverInterface
{
    /**
     * @var Webhooks
     */
    private $webhooksHelper;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ModuleConfig
     */
    private $config;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Webhooks $webhooksHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param ModuleConfig $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Webhooks $webhooksHelper,
        OrderRepositoryInterface $orderRepository,
        ModuleConfig $config,
        LoggerInterface $logger
    ) {
        $this->webhooksHelper = $webhooksHelper;
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->info(__('[Coke\Whitelist\Observer\StripePaymentsWebhookChargeSucceededCard] execute()'));

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $eventName = $observer->getEvent()->getName();
        $arrEvent = $observer->getData('arrEvent');

        if ($eventName == 'stripe_payments_webhook_charge_succeeded_card') {
            if (!($order = $this->webhooksHelper->loadOrderFromEvent($arrEvent))
                || !$this->config->isEnabled()
                || $order->hasInvoices()
                || !$order->getData('whitelist_status_pending')) {
                return;
            }

            $previousState = $order->getState();
            $previousStatus = $order->getStatus();

            $orderStatus = $this->config->getPendingWhitelistItemOrderStatus($order->getStoreId());
//            $order->setState($orderState);
            $order->setStatus($orderStatus);
            $this->orderRepository->save($order);

            $data = [
                'order_id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'previous_state' => $previousState,
                'state' => $order->getState(),
                'previous_status' => $previousStatus,
                'status' => $order->getStatus(),
                'event' => $eventName
            ];
            $this->logger->info(
                __('[Coke\Whitelist\Observer\StripePaymentsWebhookChargeSucceededCard] Order data: %1', print_r($data, true))
            );
        }
    }
}
