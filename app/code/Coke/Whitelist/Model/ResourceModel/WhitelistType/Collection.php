<?php
/**
 * Created by PhpStorm.
 * User: jacobsifuentes
 * Date: 11/17/20
 * Time: 1:34 PM
 */

namespace Coke\Whitelist\Model\ResourceModel\WhitelistType;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'type_id';


    protected function _construct()
    {
        $this->_init('Coke\Whitelist\Model\WhitelistType', 'Coke\Whitelist\Model\ResourceModel\WhitelistType');
    }

}