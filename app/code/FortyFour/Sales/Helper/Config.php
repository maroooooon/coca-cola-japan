<?php

namespace FortyFour\Sales\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_MAXIMUM_ORDER_AMOUNT_ACTIVE = 'sales/maximum_order/active';
    const XML_MAXIMUM_ORDER_AMOUNT_AMOUNT = 'sales/maximum_order/amount';
    const XML_MAXIMUM_ORDER_AMOUNT_ERROR_MESSAGE = 'sales/maximum_order/error_message';

    public function isMaximumOrderAmountEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_MAXIMUM_ORDER_AMOUNT_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMaximumOrderAmount($store = null): ?float
    {
        return $this->scopeConfig->getValue(
            self::XML_MAXIMUM_ORDER_AMOUNT_AMOUNT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMaximumOrderErrorMessage($store = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_MAXIMUM_ORDER_AMOUNT_ERROR_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
