<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Coke\France\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_CONFIG_ENABLED = 'coke_france/general/enabled';
    const XML_CONFIG_PRIMARY_SKU = 'coke_france/general/primary_bottle_sku';
    const XML_CONFIG_BULK_SKU = 'coke_france/general/bulk_bottle_sku';

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLED, ScopeInterface::SCOPE_STORE);
    }
    /**
     * @return string
     */
    public function primaryBottleSku(): string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PRIMARY_SKU, ScopeInterface::SCOPE_STORE);
    }
        /**
     * @return string
     */
    public function bulkBottleSku()
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_BULK_SKU, ScopeInterface::SCOPE_STORE);
    }
}

