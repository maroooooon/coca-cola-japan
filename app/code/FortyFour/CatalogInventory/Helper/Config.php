<?php

namespace FortyFour\CatalogInventory\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_MAX_QTY_FOR_ENTIRE_CART = 'cataloginventory/options/max_qty_entire_cart';

    /**
     * @param null $store
     * @return int|null
     */
    public function getMaxQtyAllowedForEntireCart($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_MAX_QTY_FOR_ENTIRE_CART,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
