<?php

namespace Coke\Whitelist\Model\ResourceModel\WhitelistOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Coke\Whitelist\Model\WhitelistOrder::class,
            \Coke\Whitelist\Model\ResourceModel\WhitelistOrder::class
        );
    }
}
