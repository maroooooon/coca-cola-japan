<?php

namespace FortyFour\ShippingAddressRestriction\Model;

use FortyFour\ShippingAddressRestriction\Api\Data\RegionServiceResponseInterface;

class RegionServiceResponse implements RegionServiceResponseInterface
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
    public function setLabel($label): RegionServiceResponseInterface
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value): RegionServiceResponseInterface
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDisable($disable): RegionServiceResponseInterface
    {
        $this->disable = $disable;
        return $this;
    }
}
