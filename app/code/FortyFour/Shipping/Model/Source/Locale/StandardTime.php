<?php

namespace FortyFour\Shipping\Model\Source\Locale;

use Magento\Framework\Data\OptionSourceInterface;

class StandardTime implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $halfHours = ["00","30"];
        $optionArray = [];
        for ($i = 0; $i < 24; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $time = substr('0' . $i, -2) . ':' . $halfHours[$j];

                $optionArray[] = [
                    'value' => $time,
                    'label' => $time
                ];
            }
        }

        return $optionArray;
    }
}
