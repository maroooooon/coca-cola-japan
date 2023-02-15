<?php

namespace Coke\Faq\Model\ResourceModel\Item;

class Collection
    extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected function _construct()
    {
        $this->_init('Coke\Faq\Model\Item', 'Coke\Faq\Model\ResourceModel\Item');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    public function addFieldToFilter($field, $condition = null)
    {
        parent::addFieldToFilter($field, $condition);
        
        return $this;
    }
}