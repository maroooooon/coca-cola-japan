<?php

namespace Coke\PostcodeRestrictions\Model\ResourceModel\Postcode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Coke\PostcodeRestrictions\Model\Postcode::class,
            \Coke\PostcodeRestrictions\Model\ResourceModel\Postcode::class
        );
    }
}