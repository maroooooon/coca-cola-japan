<?php

namespace CokeEurope\Customer\Plugin;

use CokeEurope\AddressAutocomplete\Helper\Config as ConfigHelper;

/**
 * Class LayoutProcessor
 *
 * @package CokeEurope\AddressAutocomplete\Plugin
 */
class LayoutProcessor
{


    private ConfigHelper $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    )
    {
        $this->configHelper = $configHelper;
    }

    /**
     * Function: afterProcess
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     *
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        // Remove component from layout if module is not enabled
        if (!$this->configHelper->isEnabled()) {
            return $jsLayout;
        }

        // Change sort order of address fields
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['city']['sortOrder'] = 89;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['postcode']['sortOrder'] = 91;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['country_id']['sortOrder'] = 100;

        // Add "Enter Street Address" to placeholder for shipping address
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['street']['children'][0]['placeholder'] = __('Enter Street Address');

        // Init address autocomplete on checkout street fields
        $jsLayout['components']['checkout']['children']['sidebar']['children']['address_autocomplete'] = [
            'displayArea' => 'summary',
            'component' => 'CokeEurope_AddressAutocomplete/js/autocomplete',
            'sortOrder' => '999'
        ];

        // Add address validator and address suggestions component
        if($this->configHelper->isValidateAddressEnabled()){
            // Address Validation
            $addressValidation = [
                'component' => 'CokeEurope_AddressAutocomplete/js/form/validation-element',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                ],
                'dataScope' => 'shippingAddress.custom_attributes.address_validation',
                'label' => 'Address Validation',
                'provider' => 'checkoutProvider',
                'sortOrder' => 0,
                'visible' => false,
                'validation' => [
                    'validate-address' => true
                ],
            ];

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['address_validation'] = $addressValidation;

            // Address Suggestions
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['address_suggestions'] = [
                'displayArea' => 'additional-fieldsets',
                'component' => 'CokeEurope_AddressAutocomplete/js/view/address-suggestions',
                'sortOrder' => '999'
            ];
        }


        return $jsLayout;
    }
}
