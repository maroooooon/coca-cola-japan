<?php

namespace Coke\Whitelist\Model\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    const DENIED   = 0;
    const APPROVED = 1;
    const PENDING  = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::DENIED, 'label' => __('Denied')],
            ['value' => self::APPROVED, 'label' => __('Approved')],
            ['value' => self::PENDING, 'label' => __('Pending')]
        ];
    }
}
