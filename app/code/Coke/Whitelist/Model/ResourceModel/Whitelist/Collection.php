<?php

namespace Coke\Whitelist\Model\ResourceModel\Whitelist;

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
            \Coke\Whitelist\Model\Whitelist::class,
            \Coke\Whitelist\Model\ResourceModel\Whitelist::class
        );
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray($this->getIdFieldName(), 'value');
    }
}
