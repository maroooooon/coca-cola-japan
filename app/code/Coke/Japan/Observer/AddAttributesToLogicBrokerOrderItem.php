<?php

namespace Coke\Japan\Observer;

use Coke\Japan\Model\SubscriptionDiscountAggregator;
use Coke\Japan\Model\Website;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Item;
use Psr\Log\LoggerInterface;

class AddAttributesToLogicBrokerOrderItem implements ObserverInterface
{
    /**
     * @var SubscriptionDiscountAggregator
     */
    private $subscriptionDiscountAggregator;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        SubscriptionDiscountAggregator $subscriptionDiscountAggregator,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    )
    {

        $this->subscriptionDiscountAggregator = $subscriptionDiscountAggregator;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $data = $observer->getEvent()->getDataObject();

        $apiItem = $data->getData('api_item');
        /** @var Item $item */
        $item = $data->getData('item');
        $childItem = $data->getData('child_item');
        $order = $item->getOrder();
        $trueItem = $childItem ?: $item;

        // Is japan?
        if ($order->getStore()->getWebsite()->getCode() !== Website::MARCHE) {
            return;
        }

        /**
         * can't trust $trueItem->getProduct() because that function doesn't pass 2nd parameter
         * to getById to load by store. all attributes loaded are incorrect.
         *
         * @var Product $product
         */
        $product = null;

        try {
            $product = $this->productRepository->getById($trueItem->getProductId(), $order->getStoreId());
        } catch (NoSuchEntityException $e) {
            $this->logger->critical(
                'Product not found in logicbroker send order. product = ' . $trueItem->getProductId() . ', order = ' . $order->getEntityId()
            );
        }

        $this->addExtendedAttributes($apiItem, $trueItem, $product);
        $this->addSubscriptionDiscount($apiItem, $trueItem);

        $data->setData('api_item', $apiItem);
    }

    public function addExtendedAttributes(&$apiItem, Item $item, ?Product $product)
    {
        $productOptions = $item->getProductOptions();
        $isSubscription = isset($productOptions['aw_sarp2_subscription_option']);

        $originalProductPrice = $product ? $product->getPrice() ?: '' : '';

        if ($product->getTypeId() === Product\Type::TYPE_BUNDLE) {
            $originalProductPrice = '';
        }

        // add sales unit and js code to logicbroker order item
        $apiItem['ExtendedAttributes'] = array_merge($apiItem['ExtendedAttributes'], [
            [
                'Name' => 'TaxPercentage',
                'value' => '8.00'
            ],
            [
                'Name' => 'sales_unit',
                'value' => $product ? $this->getAttributeLabel($product, 'sales_unit') ?: '' : ''
            ],
            [
                'Name' => 'js_code',
                'value' => $product ? $this->getAttributeLabel($product, 'js_code') ?: '' : ''
            ],
            [
                'Name' => 'original_product_price',
                'value' => $originalProductPrice,
            ],
            [
                'Name' => 'single_bottle_price',
                'value' => $product ? $product->getData('single_bottle_price') ?: '' : ''
            ],
            [
                'Name' => 'pack_size_number',
                'value' => $product ? preg_replace('/[^0-9]/', '', $this->getAttributeLabel($product, 'pack_size', true)) ?: '' : ''
            ],
            [
                'Name' => 'IsSubscriptionItem',
                'value' => $isSubscription ? 'Yes' : 'No',
            ]
        ]);
    }

    public function addSubscriptionDiscount(&$apiItem, Item $item)
    {
        $discount = $this->subscriptionDiscountAggregator->getItemDiscount($item);

        if ($discount === 0.00) {
            return;
        }

        if (!isset($apiItem['Discounts'])) {
            $apiItem['Discounts'] = [[
                "DiscountAmount" => 0,
                "DiscountName" => "Magento"
            ]];
        }

        $apiItem['Discounts'][0]['DiscountAmount'] = $discount;
    }

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
