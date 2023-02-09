<?php

namespace FortyFour\Quote\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_CART_MERGING_ENABLED = 'checkout/cart/cart_merging_enabled';

    /**
     * @param null $store
     * @return mixed
     */
    public function isCartMergingEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_MERGING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
