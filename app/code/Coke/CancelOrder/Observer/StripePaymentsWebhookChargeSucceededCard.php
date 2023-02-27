<?php

namespace Coke\CancelOrder\Observer;

use Coke\CancelOrder\Helper\CancelOrderHelper;
use Coke\CancelOrder\Helper\Config;
use Coke\CancelOrder\Logger\Logger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use StripeIntegration\Payments\Helper\Webhooks;

class StripePaymentsWebhookChargeSucceededCard implements ObserverInterface
{
    /**
     * @var Webhooks
     */
    private $webhooksHelper;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CancelOrderHelper
     */
    private $cancelOrderHelper;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Webhooks $webhooksHelper
     * @param Config $config
     * @param CancelOrderHelper $cancelOrderHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param Logger $logger
     */
    public function __construct(
        Webhooks $webhooksHelper,
        Config $config,
        CancelOrderHelper $cancelOrderHelper,
        OrderRepositoryInterface $orderRepository,
        Logger $logger
    ) {
        $this->webhooksHelper = $webhooksHelper;
        $this->config = $config;
        $this->cancelOrderHelper = $cancelOrderHelper;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->info(__('[StripePaymentsWebhookChargeSucceededCard] execute()'));

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $eventName = $observer->getEvent()->getName();
        $arrEvent = $observer->getData('arrEvent');

        if ($eventName == 'stripe_payments_webhook_charge_succeeded') {
            if (!($order = $this->webhooksHelper->loadOrderFromEvent($arrEvent))
                || !$this->config->isEnabled($order->getStoreId())
                || $order->hasInvoices()) {
                return;
            }

            $orderStatus = $this->config->getOrderStatus($order->getStoreId());
            $orderState = $this->cancelOrderHelper->getStateFromStatus($orderStatus);
            $order->setState($orderState);
            $order->setStatus($orderStatus);
            $this->orderRepository->save($order);

            $data = [
                'order_id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'state' => $order->getState(),
                'status' => $order->getStatus(),
                'event' => $eventName
            ];
            $this->logger->info(
                __('[StripePaymentsWebhookChargeSucceededCard] Order data: %1', print_r($data, true))
            );
        }
    }
}
