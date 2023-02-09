<?php

namespace Coke\Payment\Observer;

use Coke\Payment\Helper\CashOnDelivery as CashOnDeliveryHelper;
use Magento\Framework\Event\ObserverInterface;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SetCashOnDeliveryToProcessing implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CashOnDeliveryHelper
     */
    private $cashOnDeliveryHelper;

    /**
     * SetCashOnDeliveryToProcessing constructor.
     * @param LoggerInterface $logger
     * @param CashOnDeliveryHelper $cashOnDeliveryHelper
     */
    public function __construct(
        LoggerInterface $logger,
        CashOnDeliveryHelper $cashOnDeliveryHelper
    ) {
        $this->logger = $logger;
        $this->cashOnDeliveryHelper = $cashOnDeliveryHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');
        /** @var OrderPaymentInterface $payment */
        $payment = $order->getPayment();

        if ($payment->getMethod() == Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE
            && $this->cashOnDeliveryHelper->getShouldSetIsInProcess()) {
            $order->setIsInProcess(true);
        }
    }
}
