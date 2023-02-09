<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\Customer\ViewModel;

use CokeEurope\Customer\Helper\Config as ConfigHelper;
use CokeEurope\Checkout\Helper\Config as CheckoutHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Data implements ArgumentInterface
{

	private ConfigHelper $configHelper;
    private CheckoutHelper $checkoutHelper;

	public function __construct(
		ConfigHelper $configHelper,
        CheckoutHelper $checkoutHelper
	)
	{
		$this->configHelper = $configHelper;
        $this->checkoutHelper = $checkoutHelper;
	}

	/**
	 * Get the settings for the button that will appear on Customer Order View from system config.
	 * @return array
	 */
	public function getButtonConfig(): array
	{
		$target = $this->configHelper->getContactFormUrl();

		if ($this->configHelper->getButtonTarget()) {
			$target = $this->configHelper->getButtonTarget();
		}

		return [
			'enabled' => $this->configHelper->isButtonEnabled(),
			'title' => $this->configHelper->getButtonTitle(),
			'target' => $target,
			'color' => $this->configHelper->getButtonColor(),
			'background' => $this->configHelper->getButtonBackground()
		];
	}
}

