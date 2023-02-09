<?php
/**
 * Copyright © Bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\DataLayer\CustomerData;

use CokeEurope\DataLayer\Helper\Data;
use CokeEurope\DataLayer\Helper\Config;
use Magento\Customer\CustomerData\SectionSourceInterface;

class DataLayer implements SectionSourceInterface
{
	private Data $dataHelper;
	private Config $configHelper;

	/**
	 * @param Data $dataHelper
	 * @param Config $configHelper
	 */
	public function __construct(
		Data $dataHelper,
		Config $configHelper
	)
	{
		$this->dataHelper = $dataHelper;
		$this->configHelper = $configHelper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSectionData(): array
	{
		if (!$this->configHelper->isEnabled()) {
			return [];
		}
		return $this->dataHelper->getDataLayerSectionData();
	}
}
