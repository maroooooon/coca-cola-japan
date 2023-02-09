<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\PersonalizedProduct\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_CONFIG_PP_ENABLED = 'coke_europe/personalized_product/enabled';
	const XML_CONFIG_PRODUCT_SKU = 'coke_europe/personalized_product/personalized_product_sku';
	const XML_CONFIG_ATC_MESSAGE = 'coke_europe/personalized_product/personalized_product_atc_message';
	const XML_CONFIG_STEP_1 = 'coke_europe/personalized_product/step_1';
	const XML_CONFIG_STEP_2 = 'coke_europe/personalized_product/step_2';
	const XML_CONFIG_STEP_3 = 'coke_europe/personalized_product/step_3';

	const XML_CONFIG_CART_MAXIMUM_ENABLED = 'coke_europe/cart/cart_maximum_enabled';
	const XML_CONFIG_CART_MAXIMUM_AMOUNT = 'coke_europe/cart/cart_maximum';

	const XML_CONFIG_APPROVED_EMAIL_TEMPLATE = 'coke_europe/moderation/emails/approved_message_email/template_id';
	const XML_CONFIG_APPROVED_FROM_EMAIL = 'coke_europe/moderation/emails/approved_message_email/from_email';
	const XML_CONFIG_APPROVED_FROM_NAME = 'coke_europe/moderation/emails/approved_message_email/from_name';

	const XML_CONFIG_REJECTED_EMAIL_TEMPLATE = 'coke_europe/moderation/emails/rejected_message_email/template_id';
	const XML_CONFIG_REJECTED_FROM_EMAIL = 'coke_europe/moderation/emails/approved_message_email/from_email';
	const XML_CONFIG_REJECTED_FROM_NAME = 'coke_europe/moderation/emails/approved_message_email/from_name';

    const XML_CONFIG_MODERATION_ENABLED = 'coke_europe/moderation/enabled';
    const XML_CONFIG_MODERATION_SCRIPT = 'coke_europe/moderation/script_url';

    const XML_CONFIG_CONTACT_FORM_URL = 'trans_email/ident_general/contact_form_url';

	const XML_CONFIG_FORCE_DEFAULT_COUNTRY = 'coke_europe/general/force_default_country_code_checkout';
	const XML_CONFIG_STORE_DEFAULT_COUNTRY = 'general/country/default';

	/**
     * Function to check if the CokeEurope_PersonalizedProduct module is enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_PP_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return true if the moderation is enabled for the given website.
     * @param int $websiteId The website ID to check.
     */
    public function getWebsiteModerationIsEnabled(int $websiteId): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_MODERATION_ENABLED, ScopeInterface::SCOPE_WEBSITE,
            $websiteId);
    }

    /**
     * Return true if moderation is enabled for the given store.
     * @param int $storeId The ID of the store you want to get the configuration value for.
     */
    public function getStoreModerationIsEnabled(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_MODERATION_ENABLED, ScopeInterface::SCOPE_STORE,
            $storeId);
    }

    /**
     * Function to get personalized product step titles & descriptions from system config
     * @return array
     */
    public function getStepsConfig(): array
    {
        return [
            1 => $this->scopeConfig->getValue(self::XML_CONFIG_STEP_1, ScopeInterface::SCOPE_STORE),
            2 => $this->scopeConfig->getValue(self::XML_CONFIG_STEP_2, ScopeInterface::SCOPE_STORE),
            3 => $this->scopeConfig->getValue(self::XML_CONFIG_STEP_3, ScopeInterface::SCOPE_STORE)
        ];
    }

    /**
     * Function to get personalized product sku from system config
     * @return string|null
     */
    public function getConfigurableSku(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PRODUCT_SKU, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Function to check if moderation is enabled from system config
     * @return bool
     */
    public function getModerationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_MODERATION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Function to get enable url sku from system config
     * @return string|null
     */
	public function getModerationScript(): ?string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_MODERATION_SCRIPT, ScopeInterface::SCOPE_STORE);
	}

	/**
     * Function to get add to cart message from system config
     * @return string|null
     */
	public function getAtcMessage(): ?string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_ATC_MESSAGE, ScopeInterface::SCOPE_STORE);
	}

    /**
     * Function to get approved email template
     * @return array
     */
    public function getApprovedEmailConfigs($storeId = null): ?array
    {
        return [
            'template_id' => $this->scopeConfig->getValue(self::XML_CONFIG_APPROVED_EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE, $storeId),
            'from_email' => $this->scopeConfig->getValue(self::XML_CONFIG_APPROVED_FROM_EMAIL,
                ScopeInterface::SCOPE_STORE, $storeId),
            'from_name' => $this->scopeConfig->getValue(self::XML_CONFIG_APPROVED_FROM_NAME,
                ScopeInterface::SCOPE_STORE, $storeId),
        ];
    }

    /**
     * Function to get rejected email template
     * @return array
     */
    public function getRejectedEmailConfigs($storeId = null): ?array
    {
        return [
            'template_id' => $this->scopeConfig->getValue(self::XML_CONFIG_REJECTED_EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE, $storeId),
            'from_email' => $this->scopeConfig->getValue(self::XML_CONFIG_REJECTED_FROM_EMAIL,
                ScopeInterface::SCOPE_STORE, $storeId),
            'from_name' => $this->scopeConfig->getValue(self::XML_CONFIG_REJECTED_FROM_NAME,
                ScopeInterface::SCOPE_STORE, $storeId),
        ];
    }

    /**
     * Function to get contact form url
     * @return string
     */
    public function getContactFormUrl($storeId = null): string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_CONTACT_FORM_URL, ScopeInterface::SCOPE_STORE, $storeId);
    }

	/**
	 * Return true if the cart maximum is enabled.
	 */
	public function getCartMaximumIsEnabled(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_CART_MAXIMUM_ENABLED, ScopeInterface::SCOPE_STORE);
	}

	public function getCartMaximumAmount(): int
	{
		return (int) $this->scopeConfig->getValue(self::XML_CONFIG_CART_MAXIMUM_AMOUNT, ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Return true if the store is configured to force the default country code.
	 */
	public function isStoreForcingCountryCode(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_FORCE_DEFAULT_COUNTRY, ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get the default country code for the store.
	 * @return string The default country code for the store.
	 */
	public function getStoreDefaultCountryCode(): string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_STORE_DEFAULT_COUNTRY, ScopeInterface::SCOPE_STORE);
	}
}
