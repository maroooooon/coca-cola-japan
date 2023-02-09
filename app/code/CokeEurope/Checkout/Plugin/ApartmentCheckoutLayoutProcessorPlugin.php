<?php

/**
 * ApartmentCheckoutLayoutProcessorPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Checkout\Plugin;

use CokeEurope\Checkout\Helper\Config as CheckoutHelper;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

class ApartmentCheckoutLayoutProcessorPlugin
{
    private CheckoutHelper $checkoutHelper;

    public function __construct(CheckoutHelper $checkoutHelper)
    {
        $this->checkoutHelper = $checkoutHelper;
    }

    public function afterProcess(
        LayoutProcessor $subject,
        array $result
    ): array {
        if ($this->checkoutHelper->isUkWebsite()) {
            $apartmentFlatField = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'CokeEurope_Checkout/apartment-flat-field',
                ],
                'dataScope' => 'shippingAddress.custom_attributes.apartment-flat-field',
                'label' => '',
                'provider' => 'checkoutProvider',
                'sortOrder' => 70,
                'visible' => true,
                'placeholder' => __('Flat / Unit / Apartment')
            ];

            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['apartment-flat-field'] = $apartmentFlatField;
        }

        if ($this->checkoutHelper->isEuropeUKWebsite()){
            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['region_id']['sortOrder'] = 90;
        }

        return $result;
    }
}
