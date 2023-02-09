<?php

namespace FortyFour\ShippingAddressRestriction\Plugin;

use FortyFour\ShippingAddressRestriction\Helper\Config as ShippingAddressRestrictionConfig;
use Psr\Log\LoggerInterface;

class CustomerAddressEditBlockPlugin
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
     * @param \Magento\Customer\Block\Address\Edit $subject
     * @param $template
     * @return array
     */
    public function beforeSetTemplate(\Magento\Customer\Block\Address\Edit $subject, $template)
    {
        if ($this->shippingAddressRestrictionConfig->isEnabled()
            && str_contains($template, 'customer/address/edit.phtml')) {
            $template = 'FortyFour_ShippingAddressRestriction::customer/address/edit.phtml';
        }

        return [$template];
    }
}
