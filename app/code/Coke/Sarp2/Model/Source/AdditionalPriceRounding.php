<?php

namespace Coke\Sarp2\Model\Source;

use Aheadworks\Sarp2\Model\Plan\Source\PriceRounding;

class AdditionalPriceRounding extends PriceRounding
{
    const DOWN_TO_XX_00 = 20;
    const UP_TO_XX_00 = 21;

    public function toOptionArray()
    {
        return array_merge(parent::toOptionArray(), [
            [
                'value' => self::DOWN_TO_XX_00,
                'label' => __('Down to XX.00')
            ],
            [
                'value' => self::UP_TO_XX_00,
                'label' => __('Up to XX.00')
            ],
        ]);
    }
}
