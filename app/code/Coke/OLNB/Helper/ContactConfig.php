<?php

namespace Coke\OLNB\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class ContactConfig extends AbstractHelper
{
    const XML_PATH_COKE_CONTACT_FORM_DOB_ENABLED = 'coke_contact/form_options/enable_dob';
    const XML_PATH_COKE_CONTACT_FORM_TELEPHONE_ENABLED = 'coke_contact/form_options/enable_telephone';

    /**
     * @param null $store
     * @return mixed
     */
    public function isDobEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COKE_CONTACT_FORM_DOB_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isTelephoneEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COKE_CONTACT_FORM_DOB_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
