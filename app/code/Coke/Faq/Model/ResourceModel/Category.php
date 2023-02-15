<?php
namespace Coke\Faq\Model\ResourceModel;

class Category
    extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('faq_category','entity_id');
    }
}