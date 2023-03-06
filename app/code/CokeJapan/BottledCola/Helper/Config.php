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


    /**
     * Function to determine the Japan Store
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_JAPAN_SCOPE, ScopeInterface::SCOPE_STORE);
    }

}
