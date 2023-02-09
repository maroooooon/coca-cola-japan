<?php
/**
 * Copyright © Bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\AgeRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
	const XML_CONFIG_ENABLED = 'coke_europe/age_restriction/enabled';
	const XML_CONFIG_MIN_AGE = 'coke_europe/age_restriction/minimum_age';

	/**
	 * Function to check if the CokeEurope_AgeRestriction module is enabled
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLED, ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Function to get the minimum age as int from system config
	 * @return int
	 */
	public function getMinimumAge(): int
	{
		return (int)$this->scopeConfig->getValue(self::XML_CONFIG_MIN_AGE, ScopeInterface::SCOPE_STORE);
	}

}

