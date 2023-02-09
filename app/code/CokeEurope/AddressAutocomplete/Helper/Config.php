<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\AddressAutocomplete\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{

    const XML_CONFIG_ENABLED = 'coke_europe/address_autocomplete/enabled';
	const XML_CONFIG_VALIDATE_ADDRESS_ENABLED = 'coke_europe/address_autocomplete/validate_address_enabled';
	const XML_CONFIG_VALIDATE_POSTCODE_ENABLED = 'coke_europe/address_autocomplete/validate_postcode_enabled';
    const XML_CONFIG_API_KEY = 'coke_europe/address_autocomplete/api_key';


    /**
     * Function to check if the CokeEurope_AddressAutocomplete module is enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isValidateAddressEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_VALIDATE_ADDRESS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isValidatePostcodeEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_VALIDATE_POSTCODE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Function to get the Google address autocomplete api key from system config
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_API_KEY, ScopeInterface::SCOPE_STORE);
    }

}
