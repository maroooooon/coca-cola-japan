<?php
/**
 * Created by PhpStorm.
 * User: jacobsifuentes
 * Date: 11/17/20
 * Time: 1:34 PM
 */

namespace Coke\Whitelist\Model\ResourceModel;

class WhitelistType extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('coke_whitelist_types', 'type_id');
    }

}