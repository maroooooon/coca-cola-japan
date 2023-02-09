<?php

namespace Coke\Whitelist\Plugin;

use Coke\Whitelist\Model\ModuleConfig;
use Magento\Sales\Model\Order;

class SalesOrderPlugin
{
    /**
     * Retrieve order invoice availability
     *
     * @param Order $subject
     * @param       $result
     *
     * @return false|mixed
     */
    public function afterCanInvoice(Order $subject, $result)
    {
        if ($subject->getStatus() === ModuleConfig::XML_PATH_ORDER_STATUS) {
            $result = false;
        }

        return $result;
    }
}
