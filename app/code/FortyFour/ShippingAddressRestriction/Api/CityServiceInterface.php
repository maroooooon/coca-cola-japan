<?php

namespace FortyFour\ShippingAddressRestriction\Api;

use FortyFour\ShippingAddressRestriction\Api\Data\CityServiceResponseInterface;

interface CityServiceInterface
{
    /**
     * @return CityServiceResponseInterface[]
     */
    public function getCities();
}
