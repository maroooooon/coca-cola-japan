<?php

/**
 * LayoutProcessorPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Checkout\Plugin;

use Coke\Delivery\Helper\Data;
use Coke\Delivery\Plugin\Checkout\Block\LayoutProcessor;

class LayoutProcessorPlugin extends \Coke\Delivery\Plugin\Checkout\Block\LayoutProcessor
{
    /**
     * Plugin for the OnePage Checkout Shipping Step to rename the
     * existing textarea field to include no label and add a placeholder
     *
     * @param \Coke\Delivery\Plugin\Checkout\Block\LayoutProcessor $subject
     * @param array $result
     * @param $jsLayout
     * @return array
     */
    public function afterAfterProcess(
        LayoutProcessor $subject,
        array $result,
        $jsLayout
    ): array {

        $result["components"]["checkout"]["children"]["steps"]["children"]["shipping-step"]["children"]["shippingAddress"]["children"]["shippingAdditional"]["children"]["delivery_date"]["children"]["form-fields"]["children"]["delivery_comment"]['label'] = null;
        $result["components"]["checkout"]["children"]["steps"]["children"]["shipping-step"]["children"]["shippingAddress"]["children"]["shippingAdditional"]["children"]["delivery_date"]["children"]["form-fields"]["children"]["delivery_comment"]['placeholder'] = __('Leave a comment for delivery');


        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = [
            'agreements' => [
                'component' => 'Magento_CheckoutAgreements/js/view/checkout-agreements',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'Magento_CheckoutAgreements/checkout/checkout-agreements',
                ],
                'dataScope' => 'checkoutAgreements',
                'label' => '',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => ['required' => true],
                'sortOrder' => 1,
            ]
        ];
        $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['payments-list']['children']['before-place-order']['children']['agreements']['config']['componentDisabled'] = true;

        return $result;
    }
}
