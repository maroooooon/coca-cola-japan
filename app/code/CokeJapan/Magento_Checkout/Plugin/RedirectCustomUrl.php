<?php

namespace CokeJapan\Magento_Checkout\Plugin;

class RedirectCustomUrl
{
    public $scopeConfig;
    public $cart;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;

    }

    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result
    )
    {
        $valueFromConfig = $this->scopeConfig->getValue(
            'coke_bundledControls/bundled_controls/bundled_controls_sku',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        $valueFromStoreConfig = $this->scopeConfig->getValue(
            'coke_Japan/StoreName/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        $items = $this->cart->getQuote()->getAllItems();
        $getUserItem = array();
        $getUserAnotherItem = array();

        if ($valueFromStoreConfig === "1") {
            if ($_SERVER['REQUEST_URI'] === "/customer/account/loginPost/") {

                foreach ($items as $item) {
                    $getSku = $item->getSku();
                    array_push($getUserItem, $getSku);
                    if ($valueFromConfig !== $getSku) {
                        array_push($getUserAnotherItem, $getSku);
                    }
                }

                $customUrl = 'checkout';

                if (in_array($valueFromConfig, $getUserItem) && count($getUserAnotherItem) > 0 ){
                    $customUrl = 'checkout/cart';
                }
                $result->setPath($customUrl);
            }
        }
        return $result;
    }
}

