<?php

namespace Coke\CancelOrder\Observer;

use Coke\CancelOrder\Logger\Logger;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');

        $data = [
            'order_id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'state' => $order->getState(),
            'status' => $order->getStatus()
        ];
        $this->logger->info(
            __('[SalesModelServiceQuoteSubmitSuccess] Order data: %1', print_r($data, true))
        );
    }
}
