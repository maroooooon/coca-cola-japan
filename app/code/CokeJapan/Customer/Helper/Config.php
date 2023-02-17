<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeJapan\Customer\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_CONFIG_REDIRECT_ENABLED = 'coke_japan/customer/enable_redirect';


    /**
     * Function to check if the customer login redirect is enabled
     * @return bool
     */
    public function redirectEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_REDIRECT_ENABLED, ScopeInterface::SCOPE_STORE);
    }

}
