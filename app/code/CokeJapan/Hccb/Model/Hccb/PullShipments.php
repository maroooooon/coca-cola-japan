<?php

namespace CokeJapan\Hccb\Model\Hccb;

use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment\ItemFactory;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Shipping\Model\ShipmentNotifier;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\ShipOrder;
use CokeJapan\Hccb\Model\Logger;

class PullShipments
{
    /**
     * @var array
     */
    protected $order_skip = [];

    /**
     * @var OrderRepositoryInterface
     */
    protected $repo;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected $shipRepo;

    /**
     * @var TrackFactory
     */
    protected $trackFactory;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var ShipmentTrackRepositoryInterface
     */
    protected $trackRepo;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $search;

    /**
     * @var ShipmentNotifier
     */
    protected $notifier;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var ShipOrder
     */
    protected $shipOrder;

    /**
     * @var Logger
     */
    protected $hccbLogger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentRepositoryInterface $shipRepo
     * @param ItemFactory $itemFactory
     * @param TrackFactory $trackFactory
     * @param ShipmentTrackRepositoryInterface $trackRepo
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ShipmentNotifier $shipmentNotifier
     * @param InvoiceService $invoiceService
     * @param TransactionFactory $transactionFactory
     * @param InvoiceSender $invoiceSender
     * @param ShipOrder $shipOrder
     * @param Logger $hccbLogger
     */
    public function __construct(
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
        ShipOrder $shipOrder,
        Logger $hccbLogger
    ) {
        $this->repo               = $orderRepository;
        $this->shipRepo           = $shipRepo;
        $this->itemFactory        = $itemFactory;
        $this->trackFactory       = $trackFactory;
        $this->trackRepo          = $trackRepo;
        $this->search             = $searchCriteriaBuilder;
        $this->notifier           = $shipmentNotifier;
        $this->invoiceService     = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender      = $invoiceSender;
        $this->shipOrder      = $shipOrder;
        $this->hccbLogger         = $hccbLogger;
    }

    /**
     * Execute create shipment
     *
     * @param array $shipments
     * @return array
     */
    public function execute($shipments)
    {
        foreach ($shipments as $incrementId => $shipment) {
            try {
                $criteria = $this->search;
                $criteria->setFilterGroups([]);
                $criteria->addFilter("increment_id", $incrementId, "eq");
                $orderList = $this->repo->getList($criteria->create())->getItems();
                if (count($orderList) == 0) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Order not found for order number %1', $incrementId)
                    );
                }
                $order = reset($orderList);
                if (!$order->canShip() || !$order->hasInvoices() || $order->getStatus() != 'processing') {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('You can\'t create an shipment.')
                    );
                }

                $countItem = 0;
                foreach ($order->getItems() as $item) {
                    if ($item->getProductType() !== "configurable") {
                        $countItem ++;
                    }
                }

                if ($order->getItems() && $countItem != count($shipment['ShipmentLines'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Incorrect item number for order number %1', $incrementId)
                    );
                }

                $converted = $this->convertToMagento($shipment, $order);
                $converted->setData('customer_id', $order->getCustomerId());
                $converted->setData('shipment_number', $shipment['ShipmentNumber']);
                $converted->getExtensionAttributes()->setSourceCode('jp_marche');
                $converted->save();

                $track = $this->trackFactory->create();
                $track->setTrackNumber($shipment['ShipmentInfos']['TrackingNumber']);
                $track->setCarrierCode($shipment['ShipmentInfos']['CarrierCode']);
                $track->setTitle($shipment['ShipmentInfos']['CarrierCode']);
                $track->setParentId($converted->getEntityId());
                $track->setOrderId($converted->getOrderId());

                $converted->addTrack($track)->save();
                $converted->getOrder()->save();
                $converted->save();
                $this->notifier->notify($converted);

                #Fix for Magento issue #3307
                foreach ($converted->getAllItems() as $item) {
                    $orderItem = $item->getOrderItem();
                    $orderItem->setQtyShipped($orderItem->getQtyShipped() + $item->getQty());
                    $orderItem->save();
                }

                $order = $converted->getOrder()->load($converted->getOrder()->getId());
                $order->setState("complete")->setStatus("complete");
                $order->addCommentToStatusHistory('Order has created shipment.');
                $order->save();
            } catch (\Exception $e) {
                $this->hccbLogger->error(
                    __('Error creating shipment for Order # %1: %2', $incrementId, $e->getMessage())
                );
                $this->order_skip[] = $incrementId;
                continue;
            }
        }

        return $this->order_skip;
    }

    /**
     * ConvertToMagento
     *
     * @param array $shipment
     * @param Order $order
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convertToMagento($shipment, $order)
    {
        $ret = $this->shipRepo->create();
        $ret->setOrderId($order->getId());
        $ret->setStoreId($order->getStoreId());
        $ret->setBillingAddressId($order->getBillingAddress()->getEntityId());
        $ret->setShippingAddressId($order->getShippingAddress()->getEntityId());

        if (isset($shipment['ShipmentLines'])) {
            $items = [];
            $totalQty = 0;

            foreach ($order->getItems() as $item) {
                $productSku = $item->getSku();
                if (!isset($shipment['ShipmentLines'][$productSku]['ShipmentInfos'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Sku %1 does not exist for order number %2', $productSku, $order->getIncrementId())
                    );
                }
                if ($item->getProductType() === "configurable") {
                    continue;
                }
                if ($item->getProductType() === "simple" && $item->getParentItemId() !== null) {
                    $parent = $item->getParentItem();
                    if ($parent != null && $parent->getProductType() === "configurable") {
                        $item = $parent;
                    }
                }

                $productInfo = $shipment['ShipmentLines'][$productSku]['ShipmentInfos'];
                $newItem = $this->itemFactory->create();
                $newItem->setOrderItemId($item->getItemId());
                $newItem->setOrderItem($item);
                $newItem->setSku($item->getSku());

                $qtyShipped = $item->getQtyShipped();
                if ($qtyShipped === null) {
                    $qtyShipped = 0;
                }
                $qty = $item->getQtyOrdered() - $qtyShipped;
                $incre = $order->getIncrementId();
                if ($productInfo['Qty'] != $qty) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('%1 : The quantity required for creating shipment, does not match.', $incre)
                    );
                }

                $newItem->setQty(floatval($productInfo['Qty']));
                if ($newItem->getQty() <= 0) {
                    continue;
                }

                $totalQty += $newItem->getQty();
                $items[] = $newItem;
            }
            $ret->setTotalQty($totalQty);
            $ret->setItems($items);
        }

        return $ret;
    }
}
