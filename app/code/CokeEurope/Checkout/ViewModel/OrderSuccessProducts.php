<?php

/*
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author tanya.lamontagne
 */

namespace CokeEurope\Checkout\ViewModel;

use CokeEurope\Tax\Helper\Config as TaxConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

class OrderSuccessProducts implements ArgumentInterface
{
    public OrderFactory $orderFactory;

    public ProductRepository $productRepository;

    public StoreManagerInterface $storeManager;

    private ?Order $order;
    private float $bottleDepositTotal;

    private TaxConfig $taxHelper;

    /**
     * @param OrderFactory $orderFactory
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        OrderFactory $orderFactory,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        TaxConfig $taxHelper
    ) {
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->order = null;
        $this->bottleDepositTotal = 0.00;
        $this->taxHelper = $taxHelper;
    }

    public function getOrder(int $incrementId): Order
    {
        $orderModel = $this->orderFactory->create();
        $this->order = $orderModel->loadByIncrementId($incrementId);

        return $this->order;
    }

    public function getOrderProducts(): array
    {
        $products = [];

        foreach ($this->order->getItems() as $item) {
            /** @var Product $product */
            $product = $this->productRepository->getById($item->getProductId());

//          Add the product to the order's list of products
            $products[$item->getQtyOrdered()] = $product;

//          Add the product's possible bottle deposit fee to the total
            $this->bottleDepositTotal += $this->getBottleDepositForProduct($product);
        }

        return $products;
    }

    public function getDiscounts(Product $product): ?float
    {
        if ($product->getSpecialPrice()) {
            return ($product->getPrice() - $product->getSpecialPrice());
        }
        return null;
    }

    public function getBottleDeposit(): ?float
    {
        if ($this->bottleDepositTotal > 0) {
            return $this->bottleDepositTotal;
        }
        return null;
    }

    /**
     * @param Product $product
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBottleDepositForProduct(Product $product): ?float
    {
        $bottleDepositDetails = $product->getData('bottle_deposit_fpt');
        foreach ($bottleDepositDetails as $deposit) {
            if ($deposit['website_id'] === $this->storeManager->getStore()->getWebsiteId()) {
                return $deposit['website_id'];
            }
        }
        return null;
    }

    /**
     * It gets the total sugar tax for an order
     *
     * @param Order $order The order object
     */
    public function getTotalSugarTax(Order $order): float
    {
        return $this->taxHelper->getTotalItemsSugarTax($order);
    }
}
