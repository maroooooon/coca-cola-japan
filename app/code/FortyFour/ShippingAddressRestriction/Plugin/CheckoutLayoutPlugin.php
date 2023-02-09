<?php

namespace FortyFour\ShippingAddressRestriction\Plugin;

use FortyFour\ShippingAddressRestriction\Helper\Config as ShippingAddressRestrictionConfig;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Psr\Log\LoggerInterface;

class CheckoutLayoutPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ShippingAddressRestrictionConfig
     */
    private $shippingAddressRestrictionConfig;

    /**
     * CheckoutLayoutPlugin constructor.
     * @param LoggerInterface $logger
     * @param ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig
     */
    public function __construct(
        LoggerInterface $logger,
        ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig
    ) {
        $this->logger = $logger;
        $this->shippingAddressRestrictionConfig = $shippingAddressRestrictionConfig;
    }

    /**
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        $jsLayout
    ) {
        if (!$this->shippingAddressRestrictionConfig->isEnabled()) {
            return $jsLayout;
        }

        $jsLayout = $this->updateCityField($jsLayout);

        if ($this->shippingAddressRestrictionConfig->canApplyToRegion()) {
            $jsLayout = $this->updateRegionField($jsLayout);
            $jsLayout = $this->removeRegionIdField($jsLayout);
        }

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function updateCityField($jsLayout)
    {
        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'])) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['config']['elementTmpl']
            = 'FortyFour_ShippingAddressRestriction/form/element/city';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['component']
            = 'FortyFour_ShippingAddressRestriction/js/form/element/city';

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function updateRegionField($jsLayout)
    {
        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region'])) {
            return $jsLayout;
        }

        $data = [
            'component' => 'FortyFour_ShippingAddressRestriction/js/form/element/region',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'FortyFour_ShippingAddressRestriction/form/element/region'
            ],
            'dataScope' => 'shippingAddress.region',
            'label' => __('State/Province'),

            'provider' => 'checkoutProvider',
            'sortOrder' => '100',
            'visible' => 1
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region'] = $data;

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function removeRegionIdField($jsLayout)
    {
        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id'])) {
            return $jsLayout;
        }

        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']);

        return $jsLayout;
    }
}
