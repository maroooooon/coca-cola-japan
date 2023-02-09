<?php

namespace Coke\Japan\Plugin;

class Sarp2ProfileSourcePlugin
{
    /**
     * 'Pending' status
     */
    const PENDING = 'pending';

    /**
     * 'Active' status
     */
    const ACTIVE = 'active';

    /**
     * 'Suspended' status
     */
    const SUSPENDED = 'suspended';

    /**
     * 'Cancelled' status
     */
    const CANCELLED = 'cancelled';

    /**
     * 'Expired' status
     */
    const EXPIRED = 'expired';

    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function aftertoOptionArray()
    {
        $options = [ 
                [
                    'value' => self::PENDING,
                    'label' => __('Pending')
                ],
                [
                    'value' => self::ACTIVE,
                    'label' => __('Ongoing')
                ],
                [
                    'value' => self::SUSPENDED,
                    'label' => __('Suspended')
                ],
                [
                    'value' => self::CANCELLED,
                    'label' => __('Cancelled')
                ],
                [
                    'value' => self::EXPIRED,
                    'label' => __('Expired')
                ]
            ];
        return $options;
    }
}

