<?php

namespace Coke\CancelOrder\Model\Config\Source;

class CronSchedule implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '*/5 * * * *', 'label' => 'Every 5 Minutes'],
            ['value' => '*/10 * * * *', 'label' => 'Every 10 Minutes'],
            ['value' => '*/15 * * * *', 'label' => 'Every 15 Minutes'],
            ['value' => '*/20 * * * *', 'label' => 'Every 20 Minutes'],
            ['value' => '*/25 * * * *', 'label' => 'Every 30 Minutes'],
            ['value' => '0 * * * *', 'label' => 'Every hour']
        ];
    }
}
