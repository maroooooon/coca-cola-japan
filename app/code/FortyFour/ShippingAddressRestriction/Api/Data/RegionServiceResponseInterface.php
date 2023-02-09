<?php

namespace FortyFour\ShippingAddressRestriction\Api\Data;

interface RegionServiceResponseInterface
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
    public function setLabel($label): RegionServiceResponseInterface;

    /**
     * @param $value
     * @return RegionServiceResponseInterface
     */
    public function setValue($value): RegionServiceResponseInterface;

    /**
     * @param $disable
     * @return RegionServiceResponseInterface
     */
    public function setDisable($disable): RegionServiceResponseInterface;
}
