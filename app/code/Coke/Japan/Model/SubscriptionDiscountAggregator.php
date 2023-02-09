<?php

namespace Coke\Japan\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

class SubscriptionDiscountAggregator
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var array
     */
    private $product;

    /**
     * SubscriptionDiscountAggregator constructor.
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    )
    {
        $this->productRepository = $productRepository;
    }

    public function getItemDiscount(Item $item): float
    {
        $productOptions = $item->getProductOptions();

        $isSubscription = isset($productOptions['aw_sarp2_subscription_plan']);
        if (!$isSubscription) {
            return 0.00;
        }

        if ($item->getPrice() == 0) {
            return 0.00;
        }

        if (($product = $this->getProductById($item->getProductId()))
            && $product->getId()
            && $product->getPrice() > 0) {
            return ceil($product->getPrice() - $item->getPrice());
        }

        $regularPricePercent = $productOptions['aw_sarp2_subscription_plan']['regular_price_pattern_percent'];
        $discountPercent = 100 - $regularPricePercent;

        // japan rounds down their prices, so we have round up our discounts.
        return ceil(($item->getPrice() / (1 - ($discountPercent / 100))) - $item->getPrice());
    }

    public function getOrderDiscount(Order $order): float
    {
        return array_reduce($order->getAllVisibleItems(), function ($carry, $item) {
            /** @var Item $item */
            return $carry + ($this->getItemDiscount($item) * $item->getQtyOrdered());
        }, 0);
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|null
     */
    private function getProductById(int $productId)
    {
        try {
            if (!isset($this->product[$productId])) {
                $this->product[$productId] = $this->productRepository->getById($productId);
            }
            return $this->product[$productId];
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
