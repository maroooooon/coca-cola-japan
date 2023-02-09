<?php

namespace FortyFour\ShippingAddressRestriction\Model;

use FortyFour\ShippingAddressRestriction\Api\Data\CityServiceResponseInterface;

class CityServiceResponse implements CityServiceResponseInterface
{
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    private $value;
    /**
     * @var int
     */
    private $disable;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function getDisable(): int
    {
        return $this->disable;
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label): CityServiceResponseInterface
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value): CityServiceResponseInterface
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDisable($disable): CityServiceResponseInterface
    {
        $this->disable = $disable;
        return $this;
    }
}
