<?php

namespace FortyFour\Shipping\Helper\ExpressStandard;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_STANDARD_ACTIVE = 'carriers/standard/active';
    const XML_PATH_EXPRESS_ACTIVE = 'carriers/express/active';
    const XML_PATH_EXPRESS_STANDARD_UNAVAILABLE_DAYS = 'shipping/express_standard/unavailable_days';
    const XML_PATH_EXPRESS_STANDARD_UNAVAILABLE_DATES = 'shipping/express_standard/unavailable_dates';

    /**
     * @param null $store
     * @return mixed
     */
    public function isStandardShippingEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STANDARD_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isExpressShippingEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EXPRESS_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return array|null
     */
    public function getUnavailableDays($store = null)
    {
        $unavailableDays = $this->scopeConfig->getValue(
            self::XML_PATH_EXPRESS_STANDARD_UNAVAILABLE_DAYS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return !empty($unavailableDays) ? explode(',', $unavailableDays) : [];
    }

    /**
     * @param null $store
     * @return array|null
     */
    public function getUnavailableDates($store = null)
    {
        $unavailableDates = $this->scopeConfig->getValue(
            self::XML_PATH_EXPRESS_STANDARD_UNAVAILABLE_DATES,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return !empty($unavailableDates) ? explode(',', $unavailableDates) : [];
    }
}
