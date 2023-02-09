<?php

namespace FortyFour\Voucher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_VOUCHER_ENABLED = 'voucher/settings/enabled';
    const XML_PATH_VOUCHER_SKU_QTY_LIST = 'voucher/settings/voucher_sku_qty_list';
    const XML_PATH_VOUCHER_CART_PRICE_RULE = 'voucher/settings/cart_price_rule';
    /**
     * @var Json
     */
    private $json;

    /**
     * Config constructor.
     * @param Context $context
     * @param Json $json
     */
    public function __construct(
        Context $context,
        Json $json
    ) {
        parent::__construct($context);
        $this->json = $json;
    }

    /**
     * @param null $store
     * @return int|null
     */
    public function isEnabled($store = null): ?int
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_VOUCHER_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return array|null
     */
    public function getVoucherSkuQtyList($store = null): ?array
    {
        $voucherSkuQtyList = $this->scopeConfig->getValue(
            self::XML_PATH_VOUCHER_SKU_QTY_LIST,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $voucherSkuQtyList ? $this->json->unserialize($voucherSkuQtyList) : null;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getCartPriceRuleId($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_VOUCHER_CART_PRICE_RULE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
