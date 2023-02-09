<?php

namespace Coke\PostcodeRestrictions\Model\ResourceModel;

class Postcode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('coke_postcode_restrictions_postcodes', 'id');
    }
}