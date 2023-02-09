<?php

namespace FortyFour\LazySizes\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_LAZY_SIZES_CATALOG_ENABLED = 'fortyfour_lazysizes/catalog/enabled';

    /**
     * @param null $store
     * @return bool
     */
    public function isCatalogLazyLoadingEnabled($store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_LAZY_SIZES_CATALOG_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
