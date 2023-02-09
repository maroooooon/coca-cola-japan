<?php

namespace FortyFour\Shipping\Plugin;

use FortyFour\Shipping\Helper\ExpressStandard\Config;
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
    private $expressStandardConfig;

    /**
     * CheckoutLayoutPlugin constructor.
     * @param LoggerInterface $logger
     * @param Config $expressStandardConfig
     */
    public function __construct(
        LoggerInterface $logger,
        Config $expressStandardConfig
    ) {
        $this->logger = $logger;
        $this->expressStandardConfig = $expressStandardConfig;
    }

    /**
     * @param LayoutProcessor $subject
     * @param $jsLayout
     * @return void
     */
    public function afterProcess(
        LayoutProcessor $subject,
        $jsLayout
    ) {
        if (!$this->expressStandardConfig->isExpressShippingEnabled()
            || !$this->expressStandardConfig->isStandardShippingEnabled()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = [
            'express_standard_delivery_comment' => [
                'component' => 'Magento_Ui/js/form/element/textarea',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'options' => [],
                ],
                'dataScope' => 'shippingAddress.express_standard_delivery_comment',
                'label' => __('Delivery Comment'),
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => false,
                'sortOrder' => 250,
            ]
        ];

        return $jsLayout;
    }
}
