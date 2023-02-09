<?php

namespace Coke\Whitelist\Model\Source;

class CronSchedule implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '*/4 * * * *', 'label' => 'Every 4 Minutes'],
            ['value' => '*/8 * * * *', 'label' => 'Every 8 Minutes'],
            ['value' => '*/16 * * * *', 'label' => 'Every 16 Minutes'],
            ['value' => '*/32 * * * *', 'label' => 'Every 32 Minutes'],
            ['value' => '4 * * * *', 'label' => 'Every hour']
        ];
    }
}
