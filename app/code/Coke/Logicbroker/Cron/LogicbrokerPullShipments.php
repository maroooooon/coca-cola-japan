<?php

namespace Coke\Logicbroker\Cron;

use Logicbroker\RetailerAPI\Helper\Data;
use Logicbroker\RetailerAPI\Jobs\Cron\PullShipments;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Shipment\ItemFactory;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Shipping\Model\ShipmentNotifier;

class LogicbrokerPullShipments extends PullShipments
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    public function __construct(
        Data $helper,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipRepo,
        ItemFactory $itemFactory,
        TrackFactory $trackFactory,
        ShipmentTrackRepositoryInterface $trackRepo,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShipmentNotifier $shipmentNotifier,
        InvoiceService $invoiceService,
        TransactionFactory $transactionFactory,
        InvoiceSender $invoiceSender,
        ShipmentFactory $shipmentFactory
    )
    {
        parent::__construct($helper, $orderRepository, $shipRepo, $itemFactory, $trackFactory, $trackRepo, $searchCriteriaBuilder, $shipmentNotifier, $invoiceService, $transactionFactory, $invoiceSender);
        $this->shipmentFactory = $shipmentFactory;
    }

    protected function convertToMagento($shipment)
    {
//        $ret = $this->shipRepo->create();
        // attempt to load order
        if (!isset($shipment->ExtendedAttributes)) {
            throw new \InvalidArgumentException('Shipment does not have ExtendedAttributes.');
        }

        $orderId = $this->helper->getKeyValue($shipment->ExtendedAttributes, 'SalesOrderNumber');
        if ($orderId == null) {
            throw new \Exception("Order not found, missing order number.");
        }

        $criteria = $this->search;
        $criteria->setFilterGroups(array());
        $criteria->addFilter("increment_id", $orderId, "eq");
        $orderList = $this->repo->getList($criteria->create())->getItems();

        if (count($orderList) == 0) {
            $lbKey = $shipment->Identifier->LogicbrokerKey;
            throw new \Exception("Order not found for order number ".$orderId);
        }

        /** @var Order $order */
        $order = reset($orderList);

        $ret = $this->shipmentFactory->create($order);
        if (property_exists($shipment, 'ShipmentLines')) {
            $items = array();
            $total = 0;
            foreach ($shipment->ShipmentLines as $item) {
                $newItem = $this->convertItem($item, $order);
                if ($newItem == null || $newItem->getQty() <= 0) {
                    continue;
                }

                $newItem->setShipment($ret);
                foreach ($items as $shipItem) {
                    if ($shipItem->getOrderItemId() == $newItem->getOrderItemId()
                        && $shipItem->getOrderItem()->getProductType() == "bundle") {
                        continue 2;
                    }
                }
                $total += $newItem->getQty();
                $items[] = $newItem;
            }
            $ret->setTotalQty($total);
            $ret->setItems($items);
        }
        return $ret;
    }

    protected function convertItem($item, $order)
    {
        if (!property_exists($item, 'ExtendedAttributes')) {
            return null;
        }
        $item_id = $this->helper->getKeyValue($item->ExtendedAttributes, 'item_id');
        $parentItem_id = $this->helper->getKeyValue($item->ExtendedAttributes, 'parent_item_id');

        if ($order->getStore()->getWebsite()->getCode() == \Coke\Marche\Model\Website::MARCHE) {
            $parentItem_id = null;
        }

        $parentItem = null;
        if ($parentItem_id != null) {
            $parentItem = $this->getOrderItem($order, $parentItem_id);
        }
        if ($item_id == null) {
            return null;
        }

        $orderItem = $this->getOrderItem($order, $item_id);
        if ($parentItem_id !== null && !$orderItem->isShipSeparately()) {
            return null;
        }

        $newItem = $this->itemFactory->create();
        if ($parentItem != null) {
            $newItem->setOrderItemId($parentItem_id);
            $newItem->setOrderItem($parentItem);
            $qtyShipped = $parentItem->getQtyShipped();
            if ($qtyShipped === null) {
                $qtyShipped = 0;
            }
            $qty = $parentItem->getQtyOrdered() - $qtyShipped;
            $newItem->setQty(floatval($qty));
            $newItem->setSku($parentItem->getSku());
            return $newItem;
        } else {
            $newItem->setOrderItemId($item_id);
            $orderItem = $this->getOrderItem($order, $item_id);
            if ($orderItem === null) {
                return null;
            }
            $newItem->setOrderItem($orderItem);
            $newItem->setQty(floatval($item->Quantity));
            $newItem->setSku($orderItem->getSku());
            if (!$newItem->getName()) {
                $newItem->setName($orderItem->getName());
            }

            return $newItem;
        }
        return null;
    }
}
