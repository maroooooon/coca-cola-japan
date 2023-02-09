<?php

namespace FortyFour\InputMask\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TelephoneMaskValidation implements OptionSourceInterface
{
    const VALIDATE_TELEPHONE_COMPLETE = 'validate-telephone-complete';

    /**
     * value => regex expression
     *
     * @var string[]
     */
    public static $inputMaskMap = [
        'validate-turkish-telephone-number' => '\+90 \(([\d)]{3})\) \d{3} \d{2} \d{2}',
        'validate-egypt-telephone-number' => '^\d{11,11}$'
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => null,
                'label' => __('-- Please Select --')
            ],
            [
                'value' => 'validate-turkish-telephone-number',
                'label' => '+90 (###) ### ## ##'
            ],
            [
                'value' => 'validate-egypt-telephone-number',
                'label' => '###########'
            ]
        ];
    }
}
