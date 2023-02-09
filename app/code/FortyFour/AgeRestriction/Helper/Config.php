<?php

namespace FortyFour\AgeRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_AGE_RESTRICTION_ENABLED = 'age_restriction/general/enabled';
    const XML_PATH_AGE_RESTRICTION_MIN_AGE = 'age_restriction/general/min_age';
    const XML_PATH_AGE_RESTRICTION_REDIRECT_URL_TEXT = 'age_restriction/general/redirect_url_text';
    const XML_PATH_AGE_RESTRICTION_REDIRECT_URL = 'age_restriction/general/redirect_url';
    const XML_PATH_AGE_RESTRICTION_COOKIE_LIFETIME = 'age_restriction/cookie/lifetime';

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AGE_RESTRICTION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getMinimumAgeForEntry($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AGE_RESTRICTION_MIN_AGE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getRedirectUrlText($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AGE_RESTRICTION_REDIRECT_URL_TEXT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getRedirectUrl($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AGE_RESTRICTION_REDIRECT_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getCookieLifetime($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AGE_RESTRICTION_COOKIE_LIFETIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
