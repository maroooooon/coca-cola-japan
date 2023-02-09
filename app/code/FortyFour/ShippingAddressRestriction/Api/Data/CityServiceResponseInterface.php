<?php

namespace FortyFour\ShippingAddressRestriction\Api\Data;

interface CityServiceResponseInterface
{
    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return int
     */
    public function getDisable(): int;

    /**
     * @param $label
     * @return RegionServiceResponseInterface
     */
    public function setLabel($label): CityServiceResponseInterface;

    /**
     * @param $value
     * @return RegionServiceResponseInterface
     */
    public function setValue($value): CityServiceResponseInterface;

    /**
     * @param $disable
     * @return RegionServiceResponseInterface
     */
    public function setDisable($disable): CityServiceResponseInterface;
}
