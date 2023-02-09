<?php

namespace Coke\Whitelist\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Contact extends AbstractHelper
{
    const XML_PATH_EMAIL_CC = 'contact/email/cc_email';

    /**
     * @param null $store
     * @return array|null
     */
    public function getCcEmail($store = null): ?array
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_CC,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }

        return null;
    }
}
