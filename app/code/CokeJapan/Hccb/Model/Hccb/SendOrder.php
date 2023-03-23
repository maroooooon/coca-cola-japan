<?php

namespace CokeJapan\Hccb\Model\Hccb;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\Request\Http;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryCollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product;

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
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var array
     */
    protected $ordersData = [];

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param Http $httpRequest
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param TimezoneInterface $timezone
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        Http $httpRequest,
        HistoryCollectionFactory $historyCollectionFactory,
        TimezoneInterface $timezone,
        ProductRepositoryInterface $productRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->httpRequest = $httpRequest;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->timezone = $timezone;
        $this->productRepository = $productRepository;
    }

    /**
     * Excute
     *
     * @param string $nowDate
     * @param string $timestamp
     * @return array
     */
    public function execute($nowDate, $timestamp)
    {
        $historyCollection = $this->historyCollectionFactory->create();
        $historyCollection->addFieldToFilter('status', ['eq' => self::ORDER_STATUS]);
        $historyCollection->addFieldToFilter('created_at', ['gteq' => $timestamp]);
        $historyCollection->addFieldToFilter('created_at', ['lteq' => $nowDate]);
        $historyCollection->addFieldToSelect('parent_id');

        if (!$historyCollection->count()) {
            return [];
        }

        $orderIds = [];
        foreach ($historyCollection->getData() as $product) {
            $orderIds[] = $product['parent_id'];
        }

        $orders = $this->getOrderCollection($orderIds);
        foreach ($orders as $order) {
            $this->convertOrder($order);
        }

        return $this->ordersData;
    }

    /**
     * Get Order status processing
     *
     * @param array $orderIds
     * @return OrderInterface[]
     */
    public function getOrderCollection($orderIds)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $orderIds, 'in')
            ->addFilter('status', self::ORDER_STATUS, 'eq')
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
        $i = 1;
        foreach ($order->getItems() as $item) {
            if ($item->getProductType() === "configurable") {
                continue;
            }

            /**
             * Product model
             *
             * @var Product $product
             */
            $product = $this->productRepository->getById($item->getProductId(), $order->getStoreId());
            $salesUnit = $product ? $this->getAttributeLabel($product, 'sales_unit') ?: '' : '';
            $jsCode = $product ? $this->getAttributeLabel($product, 'js_code') ?: '' : '';
            $originalProductPrice = $product ? $product->getPrice() ?: '' : '';
            $singleBottlePrice = $product ? $product->getData('single_bottle_price') ?: '' : '';
            $packSizeNumber = $product ? preg_replace(
                '/[^0-9]/',
                '',
                $this->getAttributeLabel($product, 'pack_size', true)
            ) ?: '' : '';
            $productOptions = $item->getProductOptions();
            $isSubscription = isset($productOptions['aw_sarp2_subscription_option']);
            $itemName = $item->getName();

            if ($item->getProductType() === "simple" && $item->getParentItemId() !== null) {
                $parent = $item->getParentItem();
                if ($parent != null && $parent->getProductType() === "configurable") {
                    $item = $parent;
                }
            }
            $shippingAddress = $order->getShippingAddress();
            $billingAddress = $order->getBillingAddress();
            $shippingStreetAddress1 = isset($shippingAddress->getStreet()[0]) ? $shippingAddress->getStreet()[0] : "";
            $shippingStreetAddress2 = isset($shippingAddress->getStreet()[1]) ? $shippingAddress->getStreet()[1] : "";
            $billingStreetAddress1 = isset($billingAddress->getStreet()[0]) ? $billingAddress->getStreet()[0] : "";
            $billingStreetAddress2 = isset($billingAddress->getStreet()[1]) ? $billingAddress->getStreet()[1] : "";
            $bundleItemId = $item->getParentItem() ? $item->getParentItem()->getItemId() : "";
            $created = $order->getCreatedAt();
            $time = strtotime($created);
            $createdFormat = date("m/d/Y H:i:s", $time);

            $line1 = $line2 = $labelDesign = '';
            $options = $item->getProductOptions();
            if (!empty($options['options'])) {
                foreach ($options['options'] as $option) {
                    if ($option['label'] == '1行目 入力テキスト') {
                        $line1 = $option['value'];
                    }
                    if ($option['label'] == '2行目 入力テキスト') {
                        $line2 = $option['value'];
                    }
                }
            }
            if (!empty($options['attributes_info'])) {
                foreach ($options['attributes_info'] as $option) {
                    if ($option['label'] == 'ラベルのデザインをお選びください') {
                        $labelDesign = $option['value'];
                    }
                }
            }

            if ($order->getDiscountAmount() == 0) {
                $discAmt = "0";
            }else{
                $discAmt = substr($order->getDiscountAmount(),1);
            }
            $apiOrder = [
                "Id" => $order->getEntityId(),
                "OrderNumber" => $order->getIncrementId(),
                "Date" => $createdFormat,
                "StatusCode" => $order->getStatus(),
                "SenderCompanyId" => "",
                "PartnerPO" => $order->getIncrementId(),
                "TaxPercentage" => '8.00',
                "DiscountTotal" => $discAmt,
                "SubTotal" => $order->getSubtotal(),
                "TotalAmount" => $order->getGrandTotal(),
                "ShipMethod" => $order->getShippingDescription(),
                "ShipToAddress.CompanyName" => $shippingAddress->getCompany() ?? '',
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
                "ItemIdentifier.UPC" => $item->getProduct()->getUpc() ?? '',
                "Description" => $itemName,
                "Quantity" => floor(floatval($item->getQtyOrdered())),
                "Price" => $item->getData('price') ?? "0",
                "LinkKey" => "",
                "OrderLine.ExtendedAttribute.item_Id" => $item->getItemId(),
                "OrderLine.ExtendedAttribute.bundle_item_id" => $bundleItemId,
                "OrderLine.ExtendedAttribute.sales_unit" => $salesUnit,
                "OrderLine.ExtendedAttribute.js_code" => $jsCode,
                "OrderLine.ExtendedAttribute.label_design" => $labelDesign,
                "OrderLine.ExtendedAttribute.Line_1" => $line1,
                "OrderLine.ExtendedAttribute.Line_2" => $line2,
                "OrderLine.ExtendedAttribute.pack_size_number" => $packSizeNumber,
                "OrderLine.ExtendedAttribute.single_bottle_price" => $singleBottlePrice,
                "OrderLine.ExtendedAttribute.original_product_price" => $originalProductPrice,
                "IsSubscriptionItem" => $isSubscription ? 'Yes' : 'No',
                "RewardPointsUsage" => $order->getData('reward_points_balance') ?? '',
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

            $this->ordersData[] = $apiOrder;
            $i++;
        }

        return [];
    }

    /**
     * Get Label
     *
     * @param Product $product
     * @param string $code
     * @param bool $useAdminStore
     * @return mixed|null
     */
    public function getAttributeLabel(Product $product, string $code, $useAdminStore = false)
    {
        $attr = $product->getResource()->getAttribute($code);
        if ($useAdminStore) {
            $attr->setStoreId(0);
        }

        $value = $product->getData($code);

        if ($attr->usesSource()) {
            return $attr->getSource()->getOptionText($value);
        }

        return $value;
    }
}
