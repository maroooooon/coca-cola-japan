<?php

namespace FortyFour\AgeRestriction\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RangeOneToOneHundred implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array_map(function ($i) {
            return [
                'label' => (string)$i,
                'value' => (string)$i,
            ];
        }, range(1, 100));
    }
}
