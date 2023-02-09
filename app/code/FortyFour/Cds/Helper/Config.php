<?php

namespace FortyFour\Cds\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_CDS_DISABLE_SUBSCRIPTION = 'coke_cds/general/disable_newsletter_subscription';

    /**
     * @param null $store
     * @return bool
     */
    public function isNewsletterSubscriptionDisabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CDS_DISABLE_SUBSCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
