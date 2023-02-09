<?php

namespace FortyFour\SalesRule\Model\Rule\Action\Discount;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\ByPercent;
use Magento\SalesRule\Model\Rule\Action\Discount\Data;

class ByPercentOrMaxAmountForCart extends ByPercent
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;
    /**
     * @var Rule\Action\Discount\CartFixed
     */
    private $cartFixed;

    /**
     * ByPercentOrMaxAmountForCart constructor.
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule\Action\Discount\CartFixed $cartFixed,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($validator, $discountDataFactory, $priceCurrency);
        $this->checkoutSession = $checkoutSession;
        $this->cartFixed = $cartFixed;
    }

    /**
     * @param Rule $rule
     * @param AbstractItem $item
     * @param float $qty
     * @return Data
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function calculate($rule, $item, $qty)
    {
        if (!$rule->getMaxAllowedDiscount()) {
            return $this->_calculate($rule, $item, $qty, 0);
        }
        if (!$rule->getActions()->validate($item)) {
            return $this->_calculate($rule, $item, $qty, 0);
        }
        $quote = $item->getQuote();
        $quoteItems = $quote->getAllVisibleItems();
        $quoteItems = array_filter($quoteItems, function ($item) use ($rule)   {
            return $this->validator->canApplyDiscount($item) && $rule->getActions()->validate($item);
        });
        $totalPrice = 0;
        $totalQty = count($quoteItems);

        foreach ($quoteItems as $quoteItem) {
            $totalPrice += ($quoteItem->getPrice() * $quoteItem->getQty());
        }

        $discountPercent = $rule->getDiscountAmount() / 100;
        $discountAmountByPercent = ($totalPrice * $discountPercent);
        if (!$discountAmountByPercent) {
            return $this->_calculate($rule, $item, $qty, 0);
        }
        return ($discountAmountByPercent > $rule->getMaxAllowedDiscount())
            ? $this->calculateMaxAllowedDiscount($rule, $item, $qty)
            : $this->_calculate($rule, $item, $qty, min(100, $rule->getDiscountAmount()));
    }

    /**
     * @param Rule $rule
     * @param AbstractItem $item
     * @param $totalQty
     * @return Data
     */
    public function calculateMaxAllowedDiscount($rule, $item, $totalQty)
    {
        $rule->setDiscountAmount($rule->getMaxAllowedDiscount());
        return $this->cartFixed->calculate($rule, $item, $totalQty);
    }
}
