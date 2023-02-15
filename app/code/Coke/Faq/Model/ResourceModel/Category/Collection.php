<?php

namespace Coke\Faq\Model\ResourceModel\Category;

class Collection
    extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected function _construct()
    {
        $this->_init('Coke\Faq\Model\Category', 'Coke\Faq\Model\ResourceModel\Category');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    public function addFieldToFilter($field, $condition = null)
    {
        parent::addFieldToFilter($field, $condition);
        
        return $this;
    }
    
    
}