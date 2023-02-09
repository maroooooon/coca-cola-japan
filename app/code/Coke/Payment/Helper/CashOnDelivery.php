<?php

namespace Coke\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class CashOnDelivery extends AbstractHelper
{
    const XML_PATH_COD_SET_IN_PROCESS = 'payment/cashondelivery/set_is_in_process';

    /**
     * @param null $store
     * @return mixed
     */
    public function getShouldSetIsInProcess($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COD_SET_IN_PROCESS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
