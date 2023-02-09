<?php
namespace Logicbroker\RetailerAPI\Jobs\Cron;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\GiftMessage\Api\OrderRepositoryInterface as GiftMessageRepositoryInterface;
use \Logicbroker\RetailerAPI\Helper\Data;
use Magento\Store\Model\App\Emulation;

class SendOrders
{
    protected $search;
    protected $repo;
    protected $helper;
    protected $giftRepo;
    /**
     * @var Emulation
     */
    private $emulation;

	public function __construct(
        OrderRepositoryInterface $orderRepository,
        GiftMessageRepositoryInterface $giftRepo,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $helper,
        Emulation $emulation
    ) {
        $this->repo = $orderRepository;
        $this->search = $searchCriteriaBuilder;
        $this->helper = $helper;
        $this->giftRepo = $giftRepo;
        $this->emulation = $emulation;
    }

    public function execute()
    {
        $status = 'processing';
        $criteria = $this->search;
        $criteria->addFilter("status", $status, "eq");
        $criteria->addFilter("logicbroker_key", null, "null");
        $includeBundleItems = $this->helper->getConfig(Data::BUNDLE_ITEM_ENABLE, "false") === "true";
        $orderResult = $this->repo->getList($criteria->create());
        $orders = $orderResult->getItems();
        $this->helper->logInfo("Found ".count($orders)." orders to send to API.");
        if ($this->helper->getApiKey() == null) {
            $this->helper->logInfo("API key is null, unable to transmit orders.");
            return;
        }
        foreach ($orders as $order) {
            $this->emulation->startEnvironmentEmulation($order->getStoreId(), Area::AREA_FRONTEND, true);
            $this->transmitOrder($order, $includeBundleItems);
            $this->emulation->stopEnvironmentEmulation();
        }
    }

    protected function transmitOrder($order, $includeBundle)
    {
        $this->helper->logInfo("Transmitting order ".$order->getIncrementId());
        $apiOrder = $this->convertOrder($order, $includeBundle);
		/* Checking if the conversion of the order was successful, if not, it will skip the order until next time the cron runs */
		if (!$apiOrder) {
		    $this->helper->logInfo("LogicBroker::Skipping Order ".$order->getIncrementId()." for payment related reasons");
		    return;
		}

        try {
            $json = json_encode($apiOrder);
            $id = $order->getIncrementId();
            $ch = curl_init();
            $headers = array('Accept:application/json', 'Content-Type:application/json', 'SourceSystem:Magento');
            $url = $this->helper->getApiUrl()."api/v1/orders?subscription-key=".$this->helper->getApiKey();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FAILONERROR, false);
            $result = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($result == false) {
                $this->helper->logError('Error sending order '.$id.' to API, POST failed.');
                return;
            }
            $curl_json = json_decode($result);
            if ($curl_json != null && property_exists($curl_json, 'Body') && $curl_json->Body != null
                && is_object($curl_json->Body) && property_exists($curl_json->Body, 'LogicbrokerKey')) {
                $lbKey = $curl_json->Body->LogicbrokerKey;
                $order->setLogicbrokerKey($lbKey);
                $order->save();
                $this->helper->logInfo("Updated order ".$id." with Logicbroker ID ".$lbKey);
            } else {
                $this->helper->logInfo('Status: '.$http_status);
                $this->helper->logInfo('Result: '.$result);
                $match = "This order already exists";
                if ($http_status == 400 && $result != null && strpos($result, $match) !== false) {
                    $order->setLogicbrokerKey($this->getLogicbrokerKey($result));
                    $order->save();
                    $this->helper->logInfo("Order ".$id." has a duplicate in Logicbroker's system.");
                }
            }
        } catch (\Exception $e) {
            $this->helper->logError('Error sending order '.$id.' to API: '.$e->getMessage());
        }
    }

    protected function convertOrder($order, $includeBundle)
    {
        $orderDate = null;
        $created = $order->getCreatedAt();
        if ($created) {
            $time = strtotime($created);
            $orderDate = date("Y-m-d\TH:i:s", $time);
        }
        $apiOrder = array(
          "BillToAddress" => $this->getContact($order->getBillingAddress()),
          "ShipToAddress" => $this->getContact($order->getShippingAddress()),
          "PartnerPO" => $order->getIncrementId(),
          "OrderDate" => $orderDate,
          "OrderLines" => $this->getItems($order->getItems(), $includeBundle),
          "TotalAmount" => $order->getGrandTotal(),
          "HandlingAmount" => $order->getShippingAmount(),
          "ShipmentInfos" => array(array("ClassCode" => $order->getShippingDescription())),
          "Identifier" => array("SourceKey" => $order->getIncrementId()),
          "ExtendedAttributes" => array()
        );
        $discAmt = -1 * $order->getDiscountAmount();
        $taxAmt = $order->getTaxAmount();
        if ($discAmt != 0) {
            $apiOrder["Discounts"] = array(array("DiscountAmount" => $discAmt, "DiscountName" => "Total Discount"));
        }
        if ($taxAmt != 0) {
            $apiOrder["Taxes"] = array(array("TaxAmount" => $taxAmt, "TaxTitle" => "Total Tax"));
        }
        $msg = $this->getGiftMessage($order->getEntityId());
        if ($msg !== null) {
            $apiOrder["Note"] = html_entity_decode($msg->getMessage());
            $this->setExtendedData($apiOrder["ExtendedAttributes"], "GiftMessageFrom", $msg->getSender());
            $this->setExtendedData($apiOrder["ExtendedAttributes"], "GiftMessageTo", $msg->getRecipient());
        }
        $this->setExtendedData($apiOrder["ExtendedAttributes"], "StoreId", $order->getStoreId());
        $payments = $order->getAllPayments();
        if ($payments !== null && count($payments) > 0) {
            $this->setExtendedData($apiOrder["ExtendedAttributes"], "PaymentMethod", $payments[0]->getMethod());
        }
        return $apiOrder;
    }

    protected function getGiftMessage($orderid)
    {
        try {
            $msg = $this->giftRepo->get($orderid);
            return $msg;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            # Do nothing, there is no gift message.
        }
        return null;
    }

    protected function getContact($address)
    {
        $contact = array(
          "FirstName" => $address->getFirstName(),
          "LastName" => $address->getLastName(),
          "CompanyName" => $address->getCompany(),
          "Email" => $address->getEmail(),
          "City" => $address->getCity(),
          "State" => $address->getRegionCode(),
          "Zip" => $address->getPostcode(),
          "Country" => $address->getCountryId(),
          "Phone" => $address->getTelephone()
        );
        $street = $address->getStreet();
        if ($street != null) {
            if (count($street) > 0) {
                $contact["Address1"] = $street[0];
            }
            if (count($street) > 1) {
                $contact["Address2"] = $street[1];
            }
        }
        return $contact;
    }

    protected function getItems($items, $includeBundle)
    {
        $converted = array();
        $i = 1;
        foreach ($items as $item) {
            if ($item->getProductType() === "configurable") {
                continue;
            }
            if ($item->getProductType() === "bundle" && !$includeBundle) {
                continue;
            }
            $newItem = $this->toApiItem($item);
            if ($item->getProductType() === "simple" && $item->getParentItemId() !== null) {
                $this->helper->logInfo("Parent item found: ".$item->getParentItemId());
                $parent = $item->getParentItem();
                $parentItemType = $parent->getProductType();
                if ($parent != null && $parentItemType === "configurable") {
                    $newItem = $this->toApiItem($parent, $item);
                } elseif ($parent != null && $parentItemType === "grouped") {
                    $this->setExtendedData($newItem["ExtendedAttributes"], "parent_item_id", $parent->getItemId());
                } elseif ($parent != null && $parentItemType === "bundle") {
                    $this->setExtendedData($newItem["ExtendedAttributes"], "parent_item_id", $parent->getItemId());
                    $this->setExtendedData($newItem["ExtendedAttributes"], "bundle_item_id", $parent->getItemId());
                    $this->setExtendedData($newItem["ExtendedAttributes"], "bundle_sku", $parent->getSku());
                }
            }
            $newItem["LineNumber"] = strval($i);
            $converted[] = $newItem;
            $i++;
        }
        return $converted;
    }

    protected function setExtendedData(&$ext, $name, $value)
    {
        $updated = false;
        foreach ($ext as $item) {
            if ($item["Name"] === $name) {
                $item["Value"] = $value;
                $updated = true;
                break;
            }
        }
        if ($updated == false) {
            $ext[] = array("Name" => $name, "Value" => $value);
        }
    }

    protected function getItem($items, $itemid)
    {
        foreach ($items as $item) {
            if ($item->getItemId() === $itemid) {
                return $item;
            }
        }
        return null;
    }

    protected function toApiItem($item, $childItem = null)
    {
        $name = $item->getName();
        $cost = $item->getBaseCost();
        if ($childItem != null) {
            $name = $childItem->getName();
            $cost = $childItem->getBaseCost();
        }
        $attrs = array(0 => array("Name" => "item_id", "Value" => $item->getItemId()));
        $attrs = array_merge($attrs, $this->getItemAttributes($item));
        $apiItem = array(
          "ItemIdentifier" => array(
              "SupplierSKU" => $item->getSku(),
              "PartnerSKU" => $item->getSku(),
              "UPC" => $item->getProduct()->getUpc()
          ),
          "Quantity" => floor(floatval($item->getQtyOrdered())),
          "Description" => html_entity_decode($name),
          "Price" => $item->getPrice(),
          "Cost" => $cost,
          "Weight" => $item->getWeight(),
          "ExtendedAttributes" => $attrs
        );
        $discAmt = $item->getDiscountAmount();
        $taxAmt = $item->getTaxAmount();
        if ($discAmt != 0) {
            $apiItem["Discounts"] = array(
                array(
                    "DiscountAmount" => $discAmt,
                    "DiscountName" => "Magento"
                )
            );
        }
        if ($taxAmt != 0) {
            $apiItem["Taxes"] = array(
                array(
                    "TaxAmount" => $taxAmt,
                    "TaxTitle" => "Magento"
                )
            );
        }
        return $apiItem;
    }

    protected function getItemAttributes($item)
    {
        $attrs = array();
        $options = $item->getProductOptions();
        if ($options != null
            && is_array($options)
            && array_key_exists("attributes_info", $options)
            && $options["attributes_info"] != null) {
            foreach ($options["attributes_info"] as $attr) {
                if (is_array($attr)
                && array_key_exists("label", $attr)
                && array_key_exists("value", $attr)) {
                    $attrs[] = array(
                        "Name" => "Magento_".$attr["label"],
                        "Value" => $attr["value"],
                        "Section" => "Portal"
                    );
                }
            }
        }
        return $attrs;
    }

    protected function getLogicbrokerKey($result)
    {
        try {
            $lk = strpos($result, "link key ");
            $space = strpos($result, " ", $lk + 9); //find where linkkey ends
            $linkkey = substr($result, $lk + 9, $space - $lk - 9);
            $url = $this->helper->getApiUrl()."api/v1/orders?filters.linkkey=".$linkkey;
            $apiRes = $this->helper->getFromApi($url, array('Body', 'SalesOrders'))["Result"];
            if (count($apiRes) > 0) {
                $this->helper->logInfo('Got original order key '.$apiRes[0]->LogicbrokerKey.'.');
                return $apiRes[0]->LogicbrokerKey;
            }
        } catch (\Exception $e) {
            $this->helper->logError('Error getting original order Logicbroker key from API: '.$e->getMessage());
        }
        return "0";
    }
}
