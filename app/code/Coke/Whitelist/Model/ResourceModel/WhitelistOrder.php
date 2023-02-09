<?php

namespace Coke\Whitelist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class WhitelistOrder extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('coke_whitelist_order', 'entity_id');
    }
}
