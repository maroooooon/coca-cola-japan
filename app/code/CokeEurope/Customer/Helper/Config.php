<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
	const XML_CONFIG_BUTTON_ENABLED = 'coke_europe/customer/order_view/button_enabled';
	const XML_CONFIG_BUTTON_TITLE = 'coke_europe/customer/order_view/button_title';
	const XML_CONFIG_BUTTON_TARGET = 'coke_europe/customer/order_view/button_target';
	const XML_CONFIG_BUTTON_COLOR = 'coke_europe/customer/order_view/font_color';
	const XML_CONFIG_BUTTON_BACKGROUND = 'coke_europe/customer/order_view/background_color';
	const XML_CONFIG_CONTACT_FORM_URL = 'coke_europe/contact/form_url';

	public function __construct(
		Context $context
	)
	{
		parent::__construct($context);
	}

	public function isButtonEnabled(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_BUTTON_ENABLED, ScopeInterface::SCOPE_STORE);
	}

	public function getButtonTitle(): string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_BUTTON_TITLE, ScopeInterface::SCOPE_STORE);
	}

	public function getButtonTarget(): ?string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_BUTTON_TARGET, ScopeInterface::SCOPE_STORE);
	}

	public function getButtonColor(): string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_BUTTON_COLOR, ScopeInterface::SCOPE_STORE);
	}

	public function getButtonBackground(): string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_BUTTON_BACKGROUND, ScopeInterface::SCOPE_STORE);
	}

	public function getContactFormUrl(int $storeId = null): ?string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_CONTACT_FORM_URL, ScopeInterface::SCOPE_STORE, $storeId);
	}

}

