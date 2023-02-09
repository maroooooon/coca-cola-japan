<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\Cds\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
	const XML_CONFIG_CDS_MAGENTO_WELCOME_EMAIL = 'coke_europe/cds/magento_send_welcome_email';

	/**
	 * It returns a boolean value based on the value of the XML config path.
	 */
	public function isMagentoSendWelcomeEmailConfig(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_CDS_MAGENTO_WELCOME_EMAIL, ScopeInterface::SCOPE_WEBSITE);
	}
}