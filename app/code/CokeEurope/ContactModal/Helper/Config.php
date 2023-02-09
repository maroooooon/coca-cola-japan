<?php

namespace CokeEurope\ContactModal\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
	const XML_CONFIG_CONTACT_MODAL_ENABLED = 'coke_europe/contact_modal/enabled';
	const XML_CONFIG_CONTACT_MODAL_MESSAGE = 'coke_europe/contact_modal/message';

	/**
	 * Function to check if the CokeEurope_ContactModal module is enabled
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_CONTACT_MODAL_ENABLED, ScopeInterface::SCOPE_STORE);
	}
	/**
	 * Function to get the contact modal message from system config
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_CONTACT_MODAL_MESSAGE, ScopeInterface::SCOPE_STORE);
	}
}
