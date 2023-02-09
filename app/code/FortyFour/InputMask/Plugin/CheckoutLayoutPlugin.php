<?php

namespace FortyFour\InputMask\Plugin;

use FortyFour\InputMask\Helper\Config;
use FortyFour\InputMask\Model\Source\PostcodeMaskValidation;
use FortyFour\InputMask\Model\Source\TelephoneMaskValidation;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Psr\Log\LoggerInterface;

class CheckoutLayoutPlugin
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;

    /**
     * CheckoutLayoutPlugin constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param LayoutProcessor $subject
     * @param $jsLayout
     * @return mixed
     */
    public function afterProcess(
        LayoutProcessor $subject,
        $jsLayout
    ) {
        $jsLayout = $this->addPostcodeInputMask($jsLayout);
        $jsLayout = $this->addTelephoneInputMask($jsLayout);
        $jsLayout = $this->addStreetLinesMaxLength($jsLayout);

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function addPostcodeInputMask($jsLayout)
    {
        if (!$this->config->getPostcodeInputMask()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['validation']
        [PostcodeMaskValidation::VALIDATE_POSTCODE_COMPLETE] = 1;

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function addTelephoneInputMask($jsLayout)
    {
        if (!$this->config->getTelephoneInputMask()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['validation']
        [TelephoneMaskValidation::VALIDATE_TELEPHONE_COMPLETE] = 1;

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function addStreetLinesMaxLength($jsLayout)
    {
        if (!$this->config->getMaxLengthStreetLines()) {
            return $jsLayout;
        }

        $streetFields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'];

        $streetFieldsData = [];
        foreach ($streetFields as $streetField) {
            $streetField['validation']['max_text_length'] = $this->config->getMaxLengthStreetLines();
            $streetFieldsData[] = $streetField;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children']
            = $streetFieldsData;

        return $jsLayout;
    }
}
