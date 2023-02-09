<?php

namespace Coke\DisableCheckoutEmail\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_PATH_DISABLE_CHECKOUT_EMAIL = 'coke_disablecheckoutemail/general/disable_checkout_invoice_email';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isCheckoutInvoiceEmailDisabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_DISABLE_CHECKOUT_EMAIL, ScopeInterface::SCOPE_WEBSITE);
    }
}
