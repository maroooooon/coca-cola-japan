<?php

namespace CokeEurope\Validations\Plugin;

use CokeEurope\Validations\Helper\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Store\Api\WebsiteRepositoryInterface;

class CheckoutLayoutPlugin
{
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param LayoutProcessor $subject
     * @param $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        $jsLayout
    ) {
        if ($this->config->isCheckoutPostalValidationEnabled()) {
            // Activates the postal code validation
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['validation']
            ['validate-zip-postal-code'] = 1;

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['stripe_payments-form']['children']['form-fields']
            ['children']['postcode']['validation']['validate-zip-postal-code'] = 1;

            // Activates the phone number validation
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['validation']
            ['validate-phone-number'] = 1;

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['stripe_payments-form']['children']['form-fields']
            ['children']['telephone']['validation']['validate-phone-number'] = 1;
        }

        return $jsLayout;
    }
}
