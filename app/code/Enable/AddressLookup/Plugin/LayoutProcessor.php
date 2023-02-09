<?php

namespace Enable\AddressLookup\Plugin;

use Enable\AddressLookup\Helper\Config as ConfigHelper;

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
    ): array
	{

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

		// Add Address autocomplete component
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
		['shippingAddress']['children']['shipping-address-fieldset']['children']['address_autocomplete'] = [
            'component' => 'Enable_AddressLookup/js/view/address-autocomplete',
            'label' => __('Address Lookup'),
            'value' => '',
            'visible' => true,
            'placeholder' =>  __('Start typing an address (optional)'),
            'sortOrder' => 70,
		];

        // Add hidden input for address validation
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['address_validation'] = [
            'component' => 'Enable_AddressLookup/js/form/address-validation-element',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => 'shippingAddress.custom_attributes.address_validation',
            'label' => 'Address Validation',
            'provider' => 'checkoutProvider',
            'sortOrder' => 99,
            'visible' => false,
            'validation' => [
                'validate-address' => true
            ],
        ];

        // Add address suggestions component
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']
        ['children']['address_suggestions'] = [
            'displayArea' => 'additional-fieldsets',
            'component' => 'Enable_AddressLookup/js/view/address-suggestions',
            'sortOrder' => '999'
        ];

        return $jsLayout;
    }
}
