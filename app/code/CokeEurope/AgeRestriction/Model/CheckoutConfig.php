<?php

namespace CokeEurope\AgeRestriction\Model;

use CokeEurope\AgeRestriction\Helper\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class CheckoutConfig implements ConfigProviderInterface
{

	private Config $configHelper;

	/**
	 * @param Config $configHelper
	 */
	public function __construct(
		Config $configHelper
	)
	{
		$this->configHelper = $configHelper;
	}

	/**
	 * Adds minimumAge to window.checkoutConfig using system config value;
	 * @return array
	 */
	public function getConfig(): array
	{
		$config = [];
		if(!$this->configHelper->isEnabled()){
			return $config;
		}
		$config['minimumAge'] = $this->configHelper->getMinimumAge();
		return $config;
	}
}
