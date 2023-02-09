<?php

namespace Coke\PostcodeRestrictions\Model;

class Postcode extends \Magento\Framework\Model\AbstractModel implements \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface,
    \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'coke_postcode_restrictions_postcode';
    protected $_cacheTag = 'coke_postcode_restrictions_postcode';
    protected $_eventPrefix = 'coke_postcode_restrictions_postcode';

    protected function _construct()
    {
        $this->_init(\Coke\PostcodeRestrictions\Model\ResourceModel\Postcode::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return null|string
     */
    public function getPostcode(): ?string
    {
        return $this->getData(self::postcode);
    }

    /**
     * @param string $postcode
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     */
    public function setPostcode(string $postcode)
    {
        return $this->setData(self::postcode, $postcode);
    }

    /**
     * @return null|string
     */
    public function getCity(): ?string
    {
        return $this->getData(self::CITY);
    }

    /**
     * @param string $city
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     */
    public function setCity(string $city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return !!$this->getData(self::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     */
    public function setIsActive(bool $isActive)
    {
        return $this->setData(self::IS_ACTIVE, (int)$isActive);
    }
}
