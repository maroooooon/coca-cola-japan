<?php

namespace CokeEurope\Cds\Preference;

use Coke\Cds\Helper\Cds as CdsHelper;
use Coke\Cds\Plugin\Customer\EmailNotificationPlugin;
use CokeEurope\Cds\Helper\Config;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\EmailNotificationInterface;

/**
 * Disable Account related Outgoing Emails
 *
 * Class EmailNotificationPlugin
 * @package Coke\Cds\Plugin\Customer
 */
class EmailNotificationPluginPreference extends EmailNotificationPlugin
{
	/**
	 * @var CdsHelper
	 */
	private $cdsHelper;

	private Config $cokeCdsHelper;

	/**
	 * EmailNotificationPlugin constructor.
	 * @param CdsHelper $cdsHelper
	 * @param Config $cokeCdsHelper
	 */
	public function __construct(
		CdsHelper $cdsHelper,
		Config $cokeCdsHelper
	) {
		$this->cdsHelper = $cdsHelper;
		$this->cokeCdsHelper = $cokeCdsHelper;
	}

	/**
	 * @param EmailNotificationInterface $subject
	 * @param callable $proceed
	 * @param CustomerInterface $customer
	 * @param string $type
	 * @param string $backUrl
	 * @param int $storeId
	 * @param string $sendemailStoreId
	 */
	public function aroundNewAccount(
		EmailNotificationInterface $subject,
		callable $proceed,
		CustomerInterface $customer,
       $type = EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
       $backUrl = '',
       $storeId = 0,
       $sendemailStoreId = null
	) {
		if (!$this->cdsHelper->isEnabled() || $this->cokeCdsHelper->isMagentoSendWelcomeEmailConfig()) {
			$proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
		}

		// Do nothing
	}
}
