<?php

namespace CokeJapan\Hccb\Model\Hccb;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\Request\Http;

class SendOrder
{
    private const ORDER_STATUS = 'processing';

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param Http $httpRequest
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        Http $httpRequest
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->httpRequest = $httpRequest;
    }

    /**
     * Execute
     *
     * @return array
     */
    public function execute()
    {
        $ordersData = [];
        $orders = $this->getOrderCollection();

        foreach ($orders as $order) {
            $ordersData[] = $this->convertOrder($order);
            break;
        }
        return $ordersData;
    }

    /**
     * Get Order status processing
     *
     * @return OrderInterface[]
     */
    public function getOrderCollection()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', self::ORDER_STATUS)
            ->create();

        $orderList = $this->orderRepository->getList($searchCriteria);
        return $orderList->getItems();
    }

    /**
     * ConvertOrder
     *
     * @param OrderInterface $order
     * @return array
     */
    public function convertOrder($order)
    {
        $tmp = [];
        $i = 1;
        foreach ($order->getItems() as $item) {
            if ($item->getProductType() === "configurable" || $item->getProductType() === "bundle") {
                continue;
            }

            $shippingAddress = $order->getShippingAddress();
            $billingAddress = $order->getBillingAddress();
            $shippingStreetAddress1 = isset($shippingAddress->getStreet()[0]) ? $shippingAddress->getStreet()[0] : "";
            $shippingStreetAddress2 = isset($shippingAddress->getStreet()[1]) ? $shippingAddress->getStreet()[1] : "";
            $billingStreetAddress1 = isset($billingAddress->getStreet()[0]) ? $billingAddress->getStreet()[0] : "";
            $billingStreetAddress2 = isset($billingAddress->getStreet()[1]) ? $billingAddress->getStreet()[1] : "";

            $apiOrder = [
                "Id" => $order->getEntityId(),
                "OrderNumber" => $order->getIncrementId(),
                "Date" => $order->getCreatedAt(),
                "StatusCode" => $order->getStatus(),
                "SenderCompanyId" => "",
                "PartnerPO" => $order->getIncrementId(),
                "TaxPercentage" => $order->getTaxAmount(),
                "DiscountTotal" => $order->getDiscountAmount(),
                "SubTotal" => $order->getSubtotal(),
                "TotalAmount" => $order->getGrandTotal(),
                "ShipMethod" => $order->getShippingDescription(),
                "ShipToAddress.CompanyName" => $shippingAddress->getCompany(),
                "ShipToAddress.FirstName" => $shippingAddress->getFirstName(),
                "ShipToAddress.LastName" => $shippingAddress->getLastName(),
                "ShipToAddress.Address1" => $shippingStreetAddress1,
                "ShipToAddress.Address2" => $shippingStreetAddress2,
                "ShipToAddress.City" => $shippingAddress->getCity(),
                "ShipToAddress.Zip" => $shippingAddress->getPostCode(),
                "ShipToAddress.State" => $shippingAddress->getRegion(),
                "ShipToAddress.Phone" => $shippingAddress->getTelephone(),
                "LineNumber" => $i,
                "ItemIdentifier.SupplierSKU" => $item->getSku(),
                "ItemIdentifier.PartnerSKU" => $item->getSku(),
                "ItemIdentifier.UPC" => "",
                "Description" => $item->getDescription(),
                "Quantity" => $item->getQtyOrdered(),
                "Price" => $item->getPrice(),
                "LinkKey" => "",
                "OrderLine.ExtendedAttribute.item_Id" => $item->getItemId(),
                "OrderLine.ExtendedAttribute.bundle_item_id" => "",
                "OrderLine.ExtendedAttribute.sales_unit" => $order->getSalesUnit(),
                "OrderLine.ExtendedAttribute.js_code" => $item->getJsCode(),
                "OrderLine.ExtendedAttribute.label_design" => "",
                "OrderLine.ExtendedAttribute.Line_1" => "",
                "OrderLine.ExtendedAttribute.Line_2" => "",
                "OrderLine.ExtendedAttribute.pack_size_number" =>$item->getPackSizeQuantity(),
                "OrderLine.ExtendedAttribute.single_bottle_price" =>  $item->getSingleBottlePrice(),
                "OrderLine.ExtendedAttribute.original_product_price" => $item->getPrice(),
                "IsSubscriptionItem" => true,
                "RewardPointsUsage" => $order->getRewardCurrencyAmount(),
                "ShippingAmount" => $order->getShippingAmount(),
                "BillToAddress.FirstName" => $billingAddress->getFirstName(),
                "BillToAddress.LastName" => $billingAddress->getLastName(),
                "BillToAddress.Address1" => $billingStreetAddress1,
                "BillToAddress.Address2" => $billingStreetAddress2,
                "BillToAddress.City" => $billingAddress->getCity(),
                "BillToAddress.State" => $billingAddress->getRegion(),
                "BillToAddress.Zip" => $billingAddress->getPostCode(),
                "BillToAddress.Phone" => $billingAddress->getTelephone(),
            ];

            if ($item->getProductType() === "simple" && $item->getParentItemId() !== null) {
                $parent = $item->getParentItem();
                $parentItemType = $parent->getProductType();
                if ($parent != null && $parentItemType === "configurable") {
                    $apiOrder["ItemIdentifier.SupplierSKU"] = $item->getSku();
                    $apiOrder["ItemIdentifier.PartnerSKU"] = $item->getSku();
                    $apiOrder["ItemIdentifier.UPC"] = $item->getProduct()->getUpc();
                    $apiOrder["Quantity"] = floor(floatval($item->getQtyOrdered()));
                    $apiOrder["Description"] = $item->getName();
                    $apiOrder["Price"] = $item->getPrice();
                }
                if ($parent != null && $parentItemType === "bundle") {
                    $apiOrder["OrderLine.ExtendedAttribute.bundle_item_id"] = $parent->getItemId();
                }
            }

            $tmp[] = $apiOrder;
            $i++;
        }

        return $tmp;
    }
}
