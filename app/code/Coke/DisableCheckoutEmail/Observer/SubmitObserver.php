<?php

namespace Coke\DisableCheckoutEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Psr\Log\LoggerInterface;
use Coke\DisableCheckoutEmail\Model\Config;

class SubmitObserver implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param LoggerInterface $logger
     * @param OrderSender $orderSender
     * @param InvoiceSender $invoiceSender
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        OrderSender $orderSender,
        InvoiceSender $invoiceSender,
        Config $config
    ) {
        $this->logger = $logger;
        $this->orderSender = $orderSender;
        $this->invoiceSender = $invoiceSender;
        $this->config = $config;
    }

    /**
     * Send order and invoice email.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var  Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var  Order $order */
        $order = $observer->getEvent()->getOrder();

        /**
         * a flag to set that there will be redirect to third party after confirmation
         */
        $redirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();
        if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
            try {
                $this->orderSender->send($order);
                if (!$this->config->isCheckoutInvoiceEmailDisabled()) {
                    $invoice = current($order->getInvoiceCollection()->getItems());
                    if ($invoice) {
                        $this->invoiceSender->send($invoice);
                    }
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}
