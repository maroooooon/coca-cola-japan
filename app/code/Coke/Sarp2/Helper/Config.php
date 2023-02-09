<?php

namespace Coke\Sarp2\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_SARP2_SET_SHIPPING_ADDRESS_TO_ORDER = 'aw_sarp2/general/set_shipping_address_to_order';
    const XML_PATH_SARP2_FORCE_LOGIN_ENABLED = 'aw_sarp2/force_login/enabled';
    const XML_PATH_SARP2_FORCE_LOGIN_MESSAGE = 'aw_sarp2/force_login/message';
    const XML_PATH_SARP2_CAN_SKIP_NEXT_PAYMENT = 'aw_sarp2/subscription_editing/can_skip_next_payment_date';
    const XML_PATH_FREE_SHIPPING_SUBSCRIPTIONS = 'aw_sarp2/general/free_shipping_subscriptions';

    /**
     * @param null $store
     * @return bool
     */
    public function canSetShippingOnAddressToOrderConversion($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SARP2_SET_SHIPPING_ADDRESS_TO_ORDER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isForceLoginEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SARP2_FORCE_LOGIN_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getForceLoginMessage($store = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SARP2_FORCE_LOGIN_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isFreeShippingForSubscriptionsEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FREE_SHIPPING_SUBSCRIPTIONS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canSkipNextPaymentDate($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SARP2_CAN_SKIP_NEXT_PAYMENT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
