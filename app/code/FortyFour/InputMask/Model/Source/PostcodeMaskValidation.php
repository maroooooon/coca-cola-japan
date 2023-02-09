<?php

namespace FortyFour\InputMask\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PostcodeMaskValidation implements OptionSourceInterface
{
    const VALIDATE_POSTCODE_COMPLETE = 'validate-postcode-complete';

    /**
     * value => regex expression
     *
     * @var string[]
     */
    public static $inputMaskMap = [
        'validate-japan-postcode' => '^[0-9]{3}-[0-9]{4}$'
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
                'value' => 'validate-japan-postcode',
                'label' => '###-####'
            ]
        ];
    }
}
