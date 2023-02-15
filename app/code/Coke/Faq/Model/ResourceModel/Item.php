<?php
namespace Coke\Faq\Model\ResourceModel;

class Item
    extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('faq_item','entity_id');
    }
}