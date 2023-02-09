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

class UpdateQty implements ObserverInterface
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

		$items = $observer->getCart()->getQuote()->getItems();
        $info = $observer->getInfo()->getData();

        foreach ($items as $item) {
			$hasUpdate = array_key_exists($item->getId(), $info);
			// Skip items with no update
			if(!$hasUpdate){
				continue;
			}
			$oldQty = (int) $item->getQty();
			$newQty = (int) $info[$item->getId()]['qty'];
			$product = $this->dataHelper->formatProduct($item);
			// Trigger addToCart datalayer event on qty increase
			if($newQty > $oldQty){
				$qtyUpdate = $newQty - $oldQty;
				$this->dataHelper->setDatalayerCookie('dl_cart_item_added', $product, $qtyUpdate);
			}
			// Trigger removeFromCart datalayer event on qty decrease
			if($newQty < $oldQty){
				$qtyUpdate =  $oldQty - $newQty;
				$this->dataHelper->setDatalayerCookie('dl_cart_item_removed', $product, $qtyUpdate);
			}
        }

	}
}

