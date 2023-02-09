<?php

namespace CokeEurope\ContactModal\ViewModel;

use CokeEurope\ContactModal\Helper\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ContactModal implements ArgumentInterface
{
	private Config $configHelper;

	public function __construct(Config $configHelper)
	{
		$this->configHelper = $configHelper;
	}

	public function getMessage(): string
	{
		return $this->configHelper->getMessage();
	}
}
