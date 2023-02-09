<?php
/**
 * CheckoutQuoteItemStatusPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Checkout\Plugin;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;

class CheckoutQuoteItemStatusPlugin
{
	private Config $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * It adds a new field to the item data array.
	 *
	 * @param AbstractItem $subject The object that called the method.
	 * @param array $result The result of the method.
	 * @param Item $item The item object
	 *
	 * @return array The result of the method.
	 */
	public function afterGetItemData(AbstractItem $subject, $result, Item $item): array
	{
		if ($this->config->getModerationEnabled() && (int) $item->getModerationStatus() === 1) {
			$result['pending_approval'] = (int) $item->getModerationStatus();
		}
		return $result;
	}
}
