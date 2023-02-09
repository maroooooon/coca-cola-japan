<?php

namespace Coke\Whitelist\Plugin;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class EmailSenderOrderSenderPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param Order\Email\Sender\OrderSender $subject
     * @param \Closure $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     * @return mixed|void
     */
    public function aroundSend(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject,
        \Closure $proceed,
        Order $order,
        $forceSyncMode = false
    ) {
        if (!$order->getData('whitelist_status_pending')) {
            return $proceed($order, $forceSyncMode);
        }
    }
}
