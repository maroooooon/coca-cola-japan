<?php
namespace Logicbroker\RetailerAPI\Jobs\Cron;

use Magento\Sales\Api\ShipmentRepositoryInterface;
use \Logicbroker\RetailerAPI\Helper\Data;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Model\Order\Shipment\ItemFactory;
use \Magento\Sales\Model\Order\Shipment\TrackFactory;
use \Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Shipping\Model\ShipmentNotifier;
use \Magento\Sales\Model\Service\InvoiceService;
use \Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class PullShipments
{
    protected $helper;
    protected $repo;
    protected $apiUrl;
    protected $apiKey;
    protected $shipRepo;
    protected $trackFactory;
    protected $itemFactory;
    protected $trackRepo;
    protected $search;
    protected $notifier;
    protected $invoiceService;
    protected $transactionFactory;
    protected $invoiceSender;

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
        InvoiceSender $invoiceSender
    ) {
        $this->helper = $helper;
        $this->repo = $orderRepository;
        $this->shipRepo = $shipRepo;
        $this->itemFactory = $itemFactory;
        $this->trackFactory = $trackFactory;
        $this->trackRepo = $trackRepo;
        $this->search = $searchCriteriaBuilder;
        $this->notifier = $shipmentNotifier;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
    }

    public function execute()
    {
        $this->apiKey = $this->helper->getApiKey();
        if ($this->apiKey == null) {
            $this->helper->logInfo("API key is null, unable to pull shipments.");
            return;
        }
        $this->apiUrl = $this->helper->getApiUrl();
        $shipCount = 1;
        while ($shipCount > 0) {
            $shipments = $this->getShipments();
            $shipCount = count($shipments);
	        foreach ($shipments as $ship) {
                $key = $ship->Identifier->LogicbrokerKey;
                if ($ship == null) {
                    $shipCount--;
                    continue;
                }
                try {
                    $converted = $this->convertToMagento($ship);
                    if ($converted == null) {
                        continue;
                    }
                    $tracking = $this->getTracking($ship);
                    $converted->save();
                    foreach ($tracking as $track) {
                        $track->setParentId($converted->getEntityId());
                        $track->setOrderId($converted->getOrderId());
                        $track->save();
                        $converted->addTrack($track);
                    }

                    $converted->save();
                    $this->helper->logInfo("Shipment ".$key." saved.");
                    $this->notifier->notify($converted);
                    #Fix for Magento issue #3307
                    foreach ($converted->getAllItems() as $item) {
                        $orderItem = $item->getOrderItem();
                        $orderItem->setQtyShipped($orderItem->getQtyShipped() + $item->getQty());
                        $orderItem->save();
                    }

                    $order = $converted->getOrder()->load($converted->getOrder()->getId());

                    $customInvoiceNumber = $this->getCustomInvoiceNumber($ship);
                    if ($customInvoiceNumber != null) {
                        $order->setCustomInvoiceNumber($customInvoiceNumber);
                    }

                    $order->save();
                    #End fix
                    if ($this->helper->getConfig(Data::INVOICE_AFTER_FIRST_SHIPMENT, "true") == "true") {
                        $invoiceSuccess = $this->tryBillUser($key, $ship, $converted);
                        if ($invoiceSuccess == false) {
                            continue;
                        }
                    }
                    $this->helper->logInfo("Updating shipment ".$key." status.");
                    $this->helper->updateDocumentStatus($this->apiUrl, $this->apiKey, 'shipment', $key, 200);
                } catch (\Exception $e) {
                    $this->helper->logError('Error creating shipment from Logicbroker ID '.$key.': '.$e->getMessage());
                    $this->helper->updateDocumentStatus($this->apiUrl, $this->apiKey, 'shipment', $key, 1200);
                    $this->helper->createFailedEvent($this->apiUrl, 'shipment', $ship, $e->getMessage());
                }
            }
        }
    }

    protected function getTracking($shipment)
    {
        $tracking = array();
        if (property_exists($shipment, 'ShipmentInfos')) {
            foreach ($shipment->ShipmentInfos as $info) {
                $this->addTracking($tracking, $info);
            }
        }
        if (property_exists($shipment, 'ShipmentLines')) {
            foreach ($shipment->ShipmentLines as $item) {
                if (property_exists($item, 'ShipmentInfos')) {
                    foreach ($item->ShipmentInfos as $info) {
                        $this->addTracking($tracking, $info);
                    }
                }
            }
        }
        $this->helper->logInfo("Returning ".count($tracking)." tracking numbers.");
        return $tracking;
    }

    protected function getCustomInvoiceNumber($shipment)
    {
        if (property_exists($shipment, 'ExtendedAttributes')) {
            $customInvoiceNumberField = $this->helper->getConfig(Data::CUSTOM_INVOICE_NUMBER, false);
            if ($customInvoiceNumberField != false) {
                return $this->helper->getKeyValue($shipment->ExtendedAttributes, $customInvoiceNumberField);
            }
        }

        return null;
    }

    protected function convertToMagento($shipment)
    {
        $ret = $this->shipRepo->create();
        $order = null;
        if (property_exists($shipment, 'ExtendedAttributes')) {
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
            $order = reset($orderList);
			if (!$order || $order === null || $order === false ){
				return false;
			}
            $ret->setOrderId($order->getId());
            $ret->setStoreId($order->getStoreId());
            $ret->setBillingAddressId($order->getBillingAddress()->getEntityId());
            $ret->setShippingAddressId($order->getShippingAddress()->getEntityId());
        }

        if (property_exists($shipment, 'ShipmentLines')) {
            $items = array();
            $total = 0;
            foreach ($shipment->ShipmentLines as $item) {
                $newItem = $this->convertItem($item, $order);
                if ($newItem == null || $newItem->getQty() <= 0) {
                    continue;
                }
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
        $parentItem = null;
        if ($parentItem_id != null) {
            $parentItem = $this->getOrderItem($order, $parentItem_id);
        }
        if ($item_id == null) {
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
            return $newItem;
        }
        return null;
    }

    protected function getOrderItem($order, $item_id)
    {
        $items = $order->getItems();
        foreach ($items as $item) {
            if ($item->getItemId() == $item_id) {
                return $item;
            }
        }
        $this->helper->logInfo("Item ".$item_id." not found on order ".$order->getIncrementId().".");
        return null;
    }

    protected function addTracking(&$tracking, $info)
    {
        $existing = null;
        if (property_exists($info, 'TrackingNumber')) {
            foreach ($tracking as $obj) {
                $trackNum = $obj->getTrackNumber();
                if ($info->TrackingNumber !== null && $trackNum == $info->TrackingNumber) {
                    $existing = $obj;
                    break;
                }
            }
            if ($existing === null) {
                $existing = $this->trackFactory->create();
                $existing->setTrackNumber($info->TrackingNumber);
                $tracking[] = $existing;
            }
            if (property_exists($info, "Qty")) {
                $existing->setQty($existing->getQty() + $info->Qty);
            }
            if (property_exists($info, "CarrierCode")) {
                $existing->setCarrierCode($info->CarrierCode);
                $existing->setTitle($info->CarrierCode);
            } elseif ($existing->getCarrier() == null) {
                $existing->setCarrierCode("custom");
                $existing->setTitle("Custom Carrier");
            }
        }
    }

    protected function getShipments()
    {
        $shipments = array();
        try {
            $url = $this->apiUrl."api/v2/shipments?filters.status=100";
            $apiRes = $this->helper->getFromApi($url, array('Records'));
            foreach ($apiRes['Result'] as $partial) {
                array_push($shipments, $partial);
            }
        } catch (\Exception $e) {
            $this->helper->logError('Error getting shipment list from v2 API: '.$e->getMessage());
        }
        return $shipments;
    }

    protected function tryBillUser($key, $ship, $converted)
    {
        try {
            $invoiceCreated = $this->billUser($converted);
            if ($invoiceCreated == true) {
                $this->helper->logInfo("Created invoice for order after shipment ".$key.".");
            }
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $this->helper->logError('Error creating invoice for shipment from Logicbroker ID '.$key.': '.$err);
            $this->helper->updateDocumentStatus($this->apiUrl, $this->apiKey, 'shipment', $key, 1200);
            $this->helper->createFailedEvent($this->apiUrl, 'shipment', $ship, 'Failed to create invoice: '.$err);
            return false;
        }
        return true;
    }

    protected function billUser($ship)
    {
        $order = $ship->getOrder();
        $items = $order->getAllItems();
        $itemsToInvoice = array();
        foreach ($items as $item) {
            $invoiced = (double)$item->getQtyInvoiced();
            if ($invoiced == 0) {
                $itemsToInvoice[$item->getItemId()] = $item->getQtyOrdered();
            }
        }
        if (count($itemsToInvoice) > 0) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $save = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
            $save->save();

            try {
                $this->invoiceSender->send($invoice);
            } catch (\Exception $e) {
                $this->helper->logError(__('Error sending invoice email during PullShipments job %1', $e->getMessage()));
            }

            return true;
        } else {
            try {
                foreach($order->getInvoiceCollection() as $_invoice) {
                    $this->invoiceSender->send($_invoice);
                }
            } catch (\Exception $e) {
                $this->helper->logError(__('Error sending invoice email during PullShipments job %1 for existing invoices.', $e->getMessage()));
            }
        }

        return false;
    }
}
