<?php

/*
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author tanya.lamontagne
 */

namespace CokeEurope\Validations\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CHECKOUT_ZIP_POSTAL_VALIDATION_ENABLED = 'coke_checkoutvalidations/general/enable_checkout_zip_postal_validations';

    /**
     * @param null $store
     * @return bool
     */
    public function isCheckoutPostalValidationEnabled($store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_ZIP_POSTAL_VALIDATION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
