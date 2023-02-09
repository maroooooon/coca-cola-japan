<?php

namespace Coke\Sarp2\Model\Source;

class Rounder extends \Aheadworks\Sarp2\Model\Product\Subscription\Price\Calculation\Rounder
{
    public function round($amount, $roundingType)
    {
        if ($roundingType == AdditionalPriceRounding::DOWN_TO_XX_00) {
            return (float)floor($amount);
        }

        if ($roundingType == AdditionalPriceRounding::UP_TO_XX_00) {
            return (float)ceil($amount);
        }

        return parent::round($amount, $roundingType);
    }
}
