<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\Tax\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Interceptor;
use Magento\Quote\Model\Quote\Item\Interceptor as ItemInterceptor;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_CONFIG_SUGAR_TAX_ENABLED = 'coke_europe/general/sugar_tax_enabled';
    private CartRepositoryInterface $cartItemRepositoryInterface;
    private ProductRepositoryInterface $productRepository;

    /**
     * @param Context $context
     */
    public function __construct(
        Context                             $context,
        CartRepositoryInterface             $cartItemRepositoryInterface,
        ProductRepositoryInterface $productRepository
    )
    {
        parent::__construct($context);
        $this->cartItemRepositoryInterface = $cartItemRepositoryInterface;
        $this->productRepository = $productRepository;
    }

    /**
     * Function to check if the CokeEurope_PersonalizedProduct module is enabled
     * @return bool
     */
    public function isEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_SUGAR_TAX_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * It loops through all the items in the cart, and for each item, it gets the sugar tax for that item, and then
     * multiplies it by the quantity of that item
     *
     * @param Interceptor $quote The order object
     * @return float The total amount of sugar tax for all items in the order.
     */
    public function getTotalItemsSugarTaxForQuote(Interceptor $quote): float
    {
        $sugarTaxTotal = 0.00;

        /* It loops through all the items in the cart, and for each item, it gets the sugar tax for that item, and then
                multiplies it by the quantity of that item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $sugarTaxTotal += $this->getItemSugarTax($item);
        }

        return $sugarTaxTotal;
    }

    /**
     * It loops through all the items in the cart, and for each item, it gets the sugar tax for that item, and then
     * multiplies it by the quantity of that item
     *
     * @param Order $order The order object
     * @return float The total amount of sugar tax for all items in the order.
     */
    public function getTotalItemsSugarTax(Order $order): float
    {
        $sugarTaxTotal = 0.00;
        $quote = $this->cartItemRepositoryInterface->get((int) $order->getQuoteId());

        /* It loops through all the items in the cart, and for each item, it gets the sugar tax for that item, and then
                multiplies it by the quantity of that item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $sugarTaxTotal  += $this->getItemSugarTax($item);
        }

        return $sugarTaxTotal;
    }

    /**
     * It loops through the items in the cart, and if the item has a sugar tax, it adds it to the total
     *
     * @param ItemInterceptor\ $item The item object
     * @return float The total sugar tax for the item.
     */
    public function getItemSugarTax(ItemInterceptor $item): float
    {
        $sugarTaxTotal = 0.00;

        if ($item->getChildren()) {
            foreach ($item->getChildren() as $child) {
                $sugarTax = (float)$child->getProduct()->getSugarTax();

                if ($sugarTax > 0) {
                    $sugarTaxTotal += $sugarTax * $item->getQty();
                }
            }
        } else {
            $sugarTax = $item->getProduct()->getSugarTax();

            if ($sugarTax > 0) {
                $sugarTaxTotal += $sugarTax * $item->getQty();
            }
        }

        return $sugarTaxTotal;
    }
}
