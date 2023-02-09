<?php

namespace CokeEurope\Checkout\Plugin;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class DefaultConfigProviderPlugin
{
	protected CheckoutSession $checkoutSession;

	public function __construct(
		CheckoutSession $checkoutSession
	) {
		$this->checkoutSession = $checkoutSession;
	}

	/**
	 * We're adding a new key to the `quoteItemData` array in the `totalsData` array in the `DefaultConfigProvider` class
	 *
	 * @param DefaultConfigProvider $subject The class that is being observed.
	 * @param array $result The result of the original method.
	 *
	 * @return array The result of the original method, with the addition of the `pending_approval` key.
	 * @throws LocalizedException
	 * @throws NoSuchEntityException
	 */
	public function afterGetConfig(
		DefaultConfigProvider $subject,
		array $result
	): array
	{
		$items = $result['totalsData']['items'];
		foreach ($items as $index => $item) {
			$quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
			$result['quoteItemData'][$index]['pending_approval'] = $quoteItem->getModerationStatus();
		}
		return $result;
	}
}
