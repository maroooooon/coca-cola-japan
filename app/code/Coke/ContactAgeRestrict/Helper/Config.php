<?php

namespace Coke\ContactAgeRestrict\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_MIN_AGE_VALIDATION = 'coke_customer/dob/min_age_validation';

    const XML_PATH_TOC_LINK = 'coke_customer/dob/toc_link';
    const XML_PATH_SAVE_DOB = 'coke_customer/dob/save_dob';

    public function getMinimumAge($store = null)
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_MIN_AGE_VALIDATION, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getTocLink($store = null)
    {
        $tocUrlKey = $this->scopeConfig->getValue(self::XML_PATH_TOC_LINK, ScopeInterface::SCOPE_STORE, $store);

        if(!$tocUrlKey) return false;

        return '<a href="' . $this->_getUrl($tocUrlKey) . '">' . __('Terms and Conditions') . '</a>';
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canSaveDob($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SAVE_DOB, ScopeInterface::SCOPE_STORE, $store);
    }
}
