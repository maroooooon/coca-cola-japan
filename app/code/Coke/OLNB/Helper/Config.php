<?php

namespace Coke\OLNB\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_SHOW_LANGUAGE_CHANGER = 'olnb/frontend/show_language_changer';
    const XML_PATH_CATALOG_HIDE_QTY_INPUT = 'olnb/catalog/hide_qty_input_pdp';
    const XML_PATH_CATALOG_HIDE_PRICE = 'olnb/catalog/hide_price_pdp';
    const XML_PATH_CHECKOUT_HIDE_CITY_STATE_INPUT = 'olnb/checkout/hide_city_state_input';



    /**
     * @return bool
     */
    public function shouldShowLanguageChanger(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_LANGUAGE_CHANGER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function shouldHideQtyInputOnPdp(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CATALOG_HIDE_QTY_INPUT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function shouldHidePricetOnPdp(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CATALOG_HIDE_PRICE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function shouldHideCityStateOnCheckout(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CHECKOUT_HIDE_CITY_STATE_INPUT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
