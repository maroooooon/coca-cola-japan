<?php

namespace FortyFour\Sales\Helper;

use Magento\Quote\Model\Quote;

class ValidateMaximumAmount
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * ValidateMaximumAmount constructor.
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function validate(Quote $quote)
    {
        if (!$this->configHelper->isMaximumOrderAmountEnabled()) {
            return true;
        }

        $maxAmount = (float)$this->configHelper->getMaximumOrderAmount();
        $grandTotal = $quote->getGrandTotal();

        return $grandTotal <= $maxAmount;
    }
}
