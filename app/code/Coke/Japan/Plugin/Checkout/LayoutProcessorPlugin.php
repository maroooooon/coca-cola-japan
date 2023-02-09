<?php

namespace Coke\Japan\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class LayoutProcessorPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CheckoutLayoutPlugin constructor.
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
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
        try {
            if ($this->storeManager->getStore()->getWebsite()->getCode() != \Coke\Japan\Model\Website::MARCHE) {
                return $jsLayout;
            }

            $jsLayout = $this->updateBillingAddressFields($jsLayout);
            $jsLayout = $this->setShippingAddressCountryFieldComponent($jsLayout);
            $jsLayout = $this->setShippingAddressPlaceholderValues($jsLayout);

            return $jsLayout;
        } catch (\Exception $e) {
            $this->logger->info(__('[LayoutProcessorPlugin] %1', $e->getMessage()));
            return $jsLayout;
        }
    }

    /**
     * @param array $jsLayout
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateBillingAddressFields(array $jsLayout): array
    {
        $fields = [
            'lastname' => '0',
            'firstname' => '10',
            'company' => '15',
            'postcode' => '20',
            'region' => '30',
            'region_id' => '40',
            'city' => '50',
            'country_id' => '60',
            'street' => '70',
            'telephone' => '80',
            'fax' => '85'
        ];

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'])) {
            $payments = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'];

            foreach ($payments as $group => $groupConfig) {
                if (!isset($groupConfig['dataScopePrefix'])) {
                    continue;
                }

                foreach ($fields as $fieldName => $sortOrder) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$group]['children']['form-fields']['children']
                    [$fieldName]['sortOrder'] = $sortOrder;

                    $jsLayout = $this->setBillingAddressCountryFieldComponent($jsLayout, $group, $fieldName);
                }
            }
        }

        return $jsLayout;
    }

    /**
     * @param array $jsLayout
     * @param $group
     * @param $fieldName
     * @return array
     */
    private function setBillingAddressCountryFieldComponent(array $jsLayout, $group, $fieldName): array
    {
        if ($fieldName == 'country_id') {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'][$group]['children']['form-fields']['children']
            [$fieldName]['component'] = 'Coke_Japan/js/form/element/country';
        }

        return $jsLayout;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
    private function setShippingAddressCountryFieldComponent(array $jsLayout): array
    {
        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['component'])) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']
        ['component'] = 'Coke_Japan/js/form/element/country';

        return $jsLayout;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
    private function setShippingAddressPlaceholderValues(array $jsLayout): array
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['placeholder'] = __('Street 1');

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]['placeholder'] = __('Street 2');
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]['label'] = __('Street Address Line 2');

        return $jsLayout;
    }
}
