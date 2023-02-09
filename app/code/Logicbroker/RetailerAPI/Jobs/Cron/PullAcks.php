<?php
namespace Logicbroker\RetailerAPI\Jobs\Cron;

use Logicbroker\RetailerAPI\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class PullAcks
{
    protected $helper;
    protected $repo;
    protected $apiUrl;
    protected $apiKey;
    protected $commentFactory;
    protected $search;

    public function __construct(
        Data $helper,
        OrderRepositoryInterface $orderRepository,
        HistoryFactory $commentFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->helper = $helper;
        $this->repo = $orderRepository;
        $this->commentFactory = $commentFactory;
        $this->search = $searchCriteriaBuilder;
    }

    public function execute()
    {
        $this->apiKey = $this->helper->getApiKey();
        if ($this->apiKey == null) {
            $this->helper->logInfo("API key is null, unable to pull acknowledgements.");
            return;
        }
        $this->apiUrl = $this->helper->getApiUrl();
        $ackCount = 1;
        $docType = 'acknowledgement';
        while ($ackCount > 0) {
            $acknowledgements = $this->getAcknowledgements();
            $ackCount = count($acknowledgements);
            foreach ($acknowledgements as $ack) {
                $key = $ack->Identifier->LogicbrokerKey;
                if ($ack == null) {
                    $ackCount--;
                    continue;
                }
                try {
                    if (property_exists($ack, 'ExtendedAttributes')) {
                        $orderId = $this->helper->getKeyValue($ack->ExtendedAttributes, 'SalesOrderNumber');
                        if ($orderId == null) {
                            throw new \Exception("Order not found, missing order number.");
                        }
                        $criteria = $this->search;
                        $criteria->setFilterGroups(array());
                        $criteria->addFilter("increment_id", $orderId, "eq");
                        $orderList = $this->repo->getList($criteria->create())->getItems();
                        $order = reset($orderList);
                        if (count($orderList) == 0 || $order == null) {
                            throw new \Exception("Order not found for order number ".$orderId);
                        }
                        $this->updateOrder($order, $ack);
                    }
                    $this->helper->updateDocumentStatus($this->apiUrl, $this->apiKey, $docType, $key, 200);
                } catch (\Exception $e) {
                    $this->helper->logError('Error updating order with acknowledgement '.$key.': '.$e->getMessage());
                    $this->helper->updateDocumentStatus($this->apiUrl, $this->apiKey, $docType, $key, 1200);
                    $this->helper->createFailedEvent($this->apiUrl, $docType, $ack, $e->getMessage());
                }
            }
        }
    }

    protected function updateOrder($order, $ack)
    {
        if (!property_exists($ack, 'AckLines') || $ack->AckLines == null) {
            return;
        }
        $itemUpdates = array("Cancelled" => array(), "Backordered" => array(), "Accepted" => array());
        foreach ($ack->AckLines as $item) {
            $qtyFields = array("Cancelled" => $item->QuantityCancelled,
                               "Accepted" => $item->Quantity,
                               "Backordered" => $item->QuantityBackordered);
            $orderItem = $this->getOrderItem($order, $item);
            if ($orderItem != null) {
                foreach ($qtyFields as $key => $value) {
                    if (array_key_exists($orderItem->getSku(), $itemUpdates[$key])) {
                        $itemUpdates[$key][$orderItem->getSku()] += $value;
                    } elseif ($value > 0) {
                        $itemUpdates[$key][$orderItem->getSku()] = $value;
                    }
                }
            }
        }
        $portalUrl = $this->helper->getPortalUrl();
        $ackId = $ack->Identifier->LogicbrokerKey;
        $portalUrl .= "/order-management/ack-details/?ackid=".$ackId;
        $comment = "Received acknowledgement (Logicbroker ID <a target='_blank' href='"
                   .$portalUrl."'>".$ackId."</a>)<br/>";
        foreach (array("Accepted", "Cancelled", "Backordered") as $item) {
            if (count($itemUpdates[$item]) > 0) {
                $comment .= $item.":<br/>";
                foreach ($itemUpdates[$item] as $key => $value) {
                    $comment .= $key.": ".$value."<br/>";
                }
                $comment .= "<br/>";
            }
        }
        if (property_exists($ack, 'ChangeReason') && $ack->ChangeReason != null) {
            $comment .= "Change reason: ".$ack->ChangeReason;
        }
        $commentEntity = $this->commentFactory->create();
        $commentEntity->setOrder($order);
        $commentEntity->setComment($comment);
        $commentEntity->save();
    }

    protected function getOrderItem($order, $item)
    {
        if (!property_exists($item, 'ExtendedAttributes')) {
            return null;
        }
        $items = $order->getItems();
        $item_id = $this->helper->getKeyValue($item->ExtendedAttributes, "item_id");
        foreach ($items as $item) {
            if ($item->getItemId() == $item_id) {
                return $item;
            }
        }
        return null;
    }

    protected function getAcknowledgements()
    {
	    $acknowledgements = array();
        try {
		$from = new \DateTime('15 minutes ago');
		$to = new \DateTime();
			
            $url = $this->apiUrl . sprintf("api/v2/acknowledgements?filters.status=100&filters.from=%s&filters.to=%s",
	            $from->format('Y-m-d\TH:i'), $to->format('Y-m-d\TH:i'));
            $apiRes = $this->helper->getFromApi($url, array('Records'));
            foreach ($apiRes['Result'] as $partial) {
                array_push($acknowledgements, $partial);
            }
        } catch (\Exception $e) {
            $this->helper->logError('Error getting acknowledgement list from API: '.$e->getMessage());
        }
        return $acknowledgements;
    }
}
