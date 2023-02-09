<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\DataLayer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
	const XML_CONFIG_ENABLED = 'coke_europe/datalayer/enabled';
	const XML_CONFIG_GTM_ID = 'coke_europe/datalayer/gtm_id';

	/**
	 * Function to check if the CokeEurope_DataLayer module is enabled
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLED, ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Function to get the GTM ID from system config
	 * @return string
	 */
	public function getGtmId(): string
	{
		return $this->scopeConfig->getValue(self::XML_CONFIG_GTM_ID, ScopeInterface::SCOPE_STORE);
	}

}
