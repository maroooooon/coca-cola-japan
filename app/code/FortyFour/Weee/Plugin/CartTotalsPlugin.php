<?php

namespace FortyFour\Weee\Plugin;

use FortyFour\Weee\Helper\SalesRule;
use Psr\Log\LoggerInterface;

class CartTotalsPlugin
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
     * CartTotalsPlugin constructor.
     * @param LoggerInterface $logger
     * @param SalesRule $salesRuleHelper
     */
    public function __construct(
        LoggerInterface $logger,
        SalesRule $salesRuleHelper
    ) {
        $this->logger = $logger;
        $this->salesRuleHelper = $salesRuleHelper;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Totals $subject
     * @param $result
     * @return false|string
     */
    public function afterGetJsLayout(\Magento\Checkout\Block\Cart\Totals $subject, $result)
    {
        if (!$this->salesRuleHelper->canApplyToFpt($subject->getQuote())) {
            return $result;
        }

        $result = json_decode($result, true);
        unset($result['components']['block-totals']['children']['weee']);
        return json_encode($result, JSON_HEX_TAG);
    }
}
