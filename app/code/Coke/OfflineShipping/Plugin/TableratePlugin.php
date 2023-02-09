<?php

namespace Coke\OfflineShipping\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\OfflineShipping\Model\Carrier\Tablerate;

class TableratePlugin
{
    /**
     * @param Tablerate $subject
     * @param callable $proceed
     * @param $type
     * @param string $code
     * @return array|mixed
     * @throws LocalizedException
     */
    public function aroundGetCode(Tablerate $subject, callable $proceed, $type, $code = '')
    {
        $codes = [
            'condition_name' => [
                'package_weight' => __('Weight vs. Destination'),
                'package_value_with_discount' => __('Price vs. Destination'),
                'package_qty' => __('# of Items vs. Destination'),
                'package_value_without_discount' => __('Order Subtotal Without Discount vs. Destination')
            ],
            'condition_name_short' => [
                'package_weight' => __('Weight (and above)'),
                'package_value_with_discount' => __('Order Subtotal (and above)'),
                'package_qty' => __('# of Items (and above)'),
                'package_value_without_discount' => __('Order Subtotal Without Discount (and above)')
            ]
        ];

        if (!isset($codes[$type])) {
            throw new LocalizedException(
                __('The "%1" code type for Table Rate is incorrect. Verify the type and try again.', $type)
            );
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new LocalizedException(
                __('The "%1: %2" code type for Table Rate is incorrect. Verify the type and try again.', $type, $code)
            );
        }

        return $codes[$type][$code];
    }

    /**
     * @param Tablerate $subject
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Quote\Model\Quote\Address\RateRequest[]
     */
    public function beforeGetRate(Tablerate $subject, \Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $request->setPackageValueWithoutDiscount($request->getPackageValue());
        return [$request];
    }
}
