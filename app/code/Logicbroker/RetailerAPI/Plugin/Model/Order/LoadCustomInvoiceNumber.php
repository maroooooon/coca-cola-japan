<?php
namespace Logicbroker\RetailerAPI\Plugin\Model\Order;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Sales\Model\OrderFactory;
use \Magento\Sales\Api\Data\OrderExtensionFactory;

class LoadCustomInvoiceNumber
{
    private $orderFactory;

    private $orderExtensionFactory;

    public function __construct(
        OrderFactory $orderFactory,
        OrderExtensionFactory $extensionFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderExtensionFactory = $extensionFactory;
    }

    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->setCustomInvoiceNumber($resultOrder);
        return $resultOrder;
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $orderSearchResult
    ) {
        foreach ($orderSearchResult->getItems() as $order) {
            $this->setCustomInvoiceNumber($order);
        }
        return $orderSearchResult;
    }

    public function setCustomInvoiceNumber(OrderInterface $order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $value = $order->getCustomInvoiceNumber();
        } else {
            $temp = $this->getOrderFactory()->create();
            $temp->load($order->getId());
            $value = $temp->getCustomInvoiceNumber();
        }

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->getOrderExtensionFactory()->create();
        $orderExtension->setCustomInvoiceNumber($value);
        $order->setExtensionAttributes($orderExtension);
    }

    public function getOrderFactory()
    {
        return $this->orderFactory;
    }

    public function getOrderExtensionFactory()
    {
        return $this->orderExtensionFactory;
    }
}
