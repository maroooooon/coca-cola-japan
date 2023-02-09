<?php

namespace FortyFour\DataLayer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_GOOGLE_BRAND_DATA_LAYER_ENABLED = 'google/brand_datalayer/enabled';

    /**
     * @param null $store
     * @return bool|null
     */
    public function isBrandDataLayerEnabled($store = null): ?bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_BRAND_DATA_LAYER_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
