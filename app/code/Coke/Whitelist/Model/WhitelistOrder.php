<?php

namespace Coke\Whitelist\Model;

use Coke\Whitelist\Api\Data\WhitelistOrderInterface;
use Coke\Whitelist\Model\ResourceModel\WhitelistOrder as WhitelistOrderResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class WhitelistOrder extends AbstractModel implements WhitelistOrderInterface, IdentityInterface
{
    const CACHE_TAG = 'coke_whitelist_whitelist_order';

    /**
     * @var string
     */
    protected $_cacheTag = 'coke_whitelist_whitelist_order';

    /**
     * @var string
     */
    protected $_eventPrefix = 'coke_whitelist_whitelist_order';

    protected function _construct()
    {
        $this->_init(WhitelistOrderResource::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getWhitlistId(): string
    {
        return $this->getData(self::WHITELIST_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWhitelistId(string $whitelistId): WhitelistOrderInterface
    {
        return $this->setData(self::WHITELIST_ID, $whitelistId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId(): string
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId(string $orderId): WhitelistOrderInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }
}
