<?php

namespace CokeEurope\AgeRestriction\Plugin;

use CokeEurope\AgeRestriction\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

class CheckoutAgeRestriction
{
	private Config $configHelper;
	private Session $customerSession;

	/**
	 * @param Config $configHelper
	 * @param Session $customerSession
	 */
	public function __construct(
		Config $configHelper,
		Session $customerSession
	)
	{
		$this->configHelper = $configHelper;
		$this->customerSession = $customerSession;
	}

	/**
	 * @param LayoutProcessor $processor
	 * @param $jsLayout
	 * @return array
	 */
	public function afterProcess(LayoutProcessor $processor, $jsLayout): array
	{
		/* Return the default layout if module is not enabled */
		if (!$this->configHelper->isEnabled()) {
			return $jsLayout;
		}

		/* Use the customers dob as the default value if available */
		$defaultValue = null;
		$customer = $this->customerSession->getCustomer();
		if($customer && $dob = $customer->getData('dob')) {
			$defaultValue = date("d/m/Y", strtotime($dob));
		}

		/* Add field for dob with custom validation to ensure customer meets minimum age requirements. */
		$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
		['shippingAddress']['children']['shipping-address-fieldset']['children']['date_of_birth'] = [
			'component' => 'Magento_Ui/js/form/element/date',
			'dataScope' => 'shippingAddress.date_of_birth',
			'provider' => 'checkoutProvider',
			'id' => 'date_of_birth',
			'label' => __('Date of Birth'),
			'value' => $defaultValue,
			'visible' => true,
			'validation' => [
				'required-entry' => true,
				'validate-age' => true
			],
			'sortOrder' => 50,
			'placeholder' => 'DD/MM/YYYY',
			'additionalClasses' => 'date',
			'config' => [
				'id' => 'date_of_birth',
				'customScope' => 'shippingAddress',
				'template' => 'ui/form/field',
				'elementTmpl' => 'ui/form/element/date',
				'options' => [
					'showWeek' => false,
					'changeMonth' => true,
					'changeYear' => true,
					'yearRange' => '1912:2022',
					'dateFormat' => "dd/MM/yy",
				]
			]
		];

		return $jsLayout;
	}
}
