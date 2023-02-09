<?php

namespace FortyFour\ShippingAddressRestriction\ViewModel\Customer;

use FortyFour\ShippingAddressRestriction\Helper\Config as ShippingAgeRestrictionConfig;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Address implements ArgumentInterface
{
    /**
     * @var ShippingAgeRestrictionConfig
     */
    private $shippingAddressRestrictionConfig;

    /**
     * Address constructor.
     * @param ShippingAgeRestrictionConfig $shippingAddressRestrictionConfig
     */
    public function __construct(
        ShippingAgeRestrictionConfig $shippingAddressRestrictionConfig
    ) {
        $this->shippingAddressRestrictionConfig = $shippingAddressRestrictionConfig;
    }

    /**
     * @return bool
     */
    public function isShippingAddressRestrictionEnabled(): bool
    {
        return $this->shippingAddressRestrictionConfig->isEnabled();
    }

    /**
     * @return bool
     */
    public function canApplyToRegion(): bool
    {
        return $this->shippingAddressRestrictionConfig->canApplyToRegion();
    }
}
