<?php

namespace Coke\CancelOrder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_CANCEL_ORDER_ACTIVE = 'cancel_order/general/enabled';
    const XML_PATH_CANCEL_ORDER_AGE_LIMIT = 'cancel_order/general/age_limit';
    const XML_PATH_CANCEL_ORDER_ORDER_STATUS = 'cancel_order/general/order_status';

    /**
     * @param null $store
     * @return bool
     */
    public function isEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CANCEL_ORDER_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getAgeLimit($store = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CANCEL_ORDER_AGE_LIMIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getOrderStatus($store = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CANCEL_ORDER_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
