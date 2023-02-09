<?php

namespace FortyFour\ShippingAddressRestriction\Api;

use FortyFour\ShippingAddressRestriction\Api\Data\RegionServiceResponseInterface;

interface RegionServiceInterface
{
    /**
     * @param string $city
     * @return RegionServiceResponseInterface[]
     */
    public function getRegionsByCity(string $city);
}
