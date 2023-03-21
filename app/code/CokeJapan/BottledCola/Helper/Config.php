<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeJapan\BottledCola\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_CONFIG_JAPAN_SCOPE = 'coke_japan/StoreName/enable';
    const PATH_BUNDLED_SKU = 'coke_bundledControls/bundled_controls/bundled_controls_sku';
    const PATH_COOKIE_LIFETIME = 'web/cookie/cookie_lifetime';

    /**
     * Function to determine the Japan Store
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_JAPAN_SCOPE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BundledSku
     *
     * @param $store
     * @return mixed
     */
    public function getBundledSku($store = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_BUNDLED_SKU,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Cookie Lifetime
     *
     * @param $store
     * @return mixed
     */
    public function getCookieLifetime($store = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_COOKIE_LIFETIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
