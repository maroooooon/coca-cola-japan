<?php

namespace FortyFour\Weee\Plugin;

use FortyFour\Weee\Helper\SalesRule;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Weee\Model\Total\Quote\WeeeTax;
use Psr\Log\LoggerInterface;

class WeeeTaxPlugin extends \Magento\Weee\Model\Total\Quote\WeeeTax
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SalesRule
     */
    private $salesRuleHelper;
    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * WeeeTaxPlugin constructor.
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param PriceCurrencyInterface $priceCurrency
     * @param LoggerInterface $logger
     * @param SalesRule $salesRuleHelper
     */
    public function __construct(
        \Magento\Weee\Helper\Data $weeeData,
        PriceCurrencyInterface $priceCurrency,
        LoggerInterface $logger,
        SalesRule $salesRuleHelper
    ) {
        parent::__construct($weeeData, $priceCurrency);
        $this->logger = $logger;
        $this->salesRuleHelper = $salesRuleHelper;
    }

    /**
     * @param WeeeTax $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return mixed
     */
    public function aroundCollect(
        \Magento\Weee\Model\Total\Quote\WeeeTax $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {

        if ($this->weeeData->isTaxable($this->_store)) {
            return $proceed($quote, $shippingAssignment, $total);
        }

        if ($this->salesRuleHelper->canApplyToFpt($quote)) {
            return $subject->processTotalAmount($total, 0, 0, 0, 0);
        }

        return $proceed($quote, $shippingAssignment, $total);
    }
}
