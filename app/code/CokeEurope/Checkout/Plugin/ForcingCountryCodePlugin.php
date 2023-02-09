<?php
/**
 * ForcingCountryCodePlugn
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Checkout\Plugin;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Checkout\Block\Onepage;

class ForcingCountryCodePlugin
{
	private Config $config;
	
	public function __construct(
		Config $config
	)
	{
		$this->config = $config;
	}
	

	/**
	 * It sets the default country to the one set in the configuration
	 *
	 * @param Onepage $subject The class that called the plugin.
	 * @param array $result The result of the original method.
	 *
	 * @return array The result of the original method.
	 */
	public function afterGetCheckoutConfig(Onepage $subject, array $result): array
	{
		if ($this->config->isStoreForcingCountryCode()) {
			$result['defaultCountryId'] = $this->config->getStoreDefaultCountryCode();
		}
		return $result;
	}
}