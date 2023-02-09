<?php
/**
 * Created by PhpStorm.
 * User: jacobsifuentes
 * Date: 11/17/20
 * Time: 1:34 PM
 */

namespace Coke\Whitelist\Model;

/**
 * @method \Coke\Whitelist\Model\ResourceModel\WhitelistType getResource()
 * @method \Coke\Whitelist\Model\ResourceModel\WhitelistType\Collection getCollection()
 */
class WhitelistType extends \Magento\Framework\Model\AbstractModel implements \Coke\Whitelist\Api\Data\WhitelistTypeInterface,
    \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'coke_whitelist_whitelisttype';
    protected $_cacheTag = 'coke_whitelist_whitelisttype';
    protected $_eventPrefix = 'coke_whitelist_whitelisttype';

    protected function _construct()
    {
        $this->_init('Coke\Whitelist\Model\ResourceModel\WhitelistType');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface
     */
    public function setName(string $name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @param string $label
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface|WhitelistType
     */
    public function setLabel(string $label)
    {
        return $this->setData(self::LABEL, $label);
    }
}
