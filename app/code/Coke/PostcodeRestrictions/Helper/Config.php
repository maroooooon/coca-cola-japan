<?php

namespace Coke\PostcodeRestrictions\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_IS_ENABLED = 'checkout/postcode_restrictions/enabled';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }
}
