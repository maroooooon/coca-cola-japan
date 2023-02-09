<?php

namespace FortyFour\ShareSite\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_SHARE_SITE_ENABLED = 'share_site/general/enabled';

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHARE_SITE_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
