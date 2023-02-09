<?php

namespace Coke\Whitelist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Whitelist extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('coke_whitelist', 'entity_id');
    }
}
