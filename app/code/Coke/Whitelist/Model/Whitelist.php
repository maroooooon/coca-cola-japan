<?php

namespace Coke\Whitelist\Model;

use Coke\Whitelist\Api\Data\WhitelistInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Whitelist extends AbstractModel implements WhitelistInterface, IdentityInterface
{
    const CACHE_TAG = 'coke_whitelist_whitelist';

    /**
     * @var string
     */
    protected $_cacheTag = 'coke_whitelist_whitelist';
    /**
     * @var string
     */
    protected $_eventPrefix = 'coke_whitelist_whitelist';

    protected function _construct()
    {
        $this->_init('Coke\Whitelist\Model\ResourceModel\Whitelist');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getTypeId(): int
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTypeId(int $typeId): WhitelistInterface
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * @inheritdoc
     */
    public function getValue(): string
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setValue(string $value): WhitelistInterface
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId): WhitelistInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status): WhitelistInterface
    {
        return $this->setData(self::STATUS, $status);
    }
}
