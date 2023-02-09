<?php
/**
 * Copyright © Bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\DataLayer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use CokeEurope\DataLayer\Helper\Data;
use CokeEurope\DataLayer\Helper\Config;

class RemoveFromCart implements ObserverInterface
{

	private Data $dataHelper;
	private Config $configHelper;

	public function __construct(
		Data   $dataHelper,
		Config $configHelper
	)
	{
		$this->dataHelper = $dataHelper;
		$this->configHelper = $configHelper;
	}


	/**
	 * @param Observer $observer
	 */
	public function execute(
		Observer $observer
	)
	{
		// Skip this observer if the CokeEurope_DataLayer module is not enabled
		if (!$this->configHelper->isEnabled()) {
			return;
		}
		// Format the product & create cookie for datalayer
		$quoteItem = $observer->getEvent()->getData('quote_item');
		$product = $this->dataHelper->formatProduct($quoteItem);
		$qtyUpdate = $quoteItem->getQty();
		$this->dataHelper->setDatalayerCookie('dl_cart_item_removed', $product, $qtyUpdate);
	}
}

