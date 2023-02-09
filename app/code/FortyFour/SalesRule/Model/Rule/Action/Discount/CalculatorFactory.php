<?php

namespace FortyFour\SalesRule\Model\Rule\Action\Discount;

use FortyFour\SalesRule\Model\Rule;

class CalculatorFactory extends \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory
{
    /**
     * @param string $type
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        $this->classByType = array_merge(
            $this->classByType,
            [
                Rule::BY_PERCENT_OR_MAX_ALLOWED_AMOUNT => ByPercentOrMaxAmountForCart::class
            ]
        );
        return parent::create($type);
    }
}
