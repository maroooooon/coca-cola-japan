<?php
/**
 * CustomerLoginMergeCartsMaxQuantity
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Catalog\Plugin;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;

class CustomerLoginMergeCartsMaxQuantity
{
	private Config $config;
	private QuoteRepository $quoteRepository;
	protected Session $_customerSession;

	public function __construct(
		Config $config,
		QuoteRepository $quoteRepository,
		Session $_customerSession
	)
	{
		$this->config = $config;
		$this->quoteRepository = $quoteRepository;
		$this->_customerSession = $_customerSession;
	}

	/**
	 * If the cart maximum is enabled, and the new quote total items is greater than the cart maximum amount, then remove all
	 * items from the quote and save the quote
	 *
	 * @param Quote $subject The quote object that is currently being merged.
	 * @param Quote $customerQuote The quote that is being merged into the current quote.
	 */
	public function beforeMerge(Quote $subject, Quote $customerQuote): void
    {
		if (!$this->config->getCartMaximumIsEnabled()) {
			return;
		}

		$maxQtyAllowedForCart = $this->config->getCartMaximumAmount();
		$newQuoteTotalItems = ($customerQuote->getItemsSummaryQty() + $subject->getItemsSummaryQty());

		if ($newQuoteTotalItems > $maxQtyAllowedForCart) {
			$subject->removeAllItems();
			$this->quoteRepository->save($subject);
		}
	}
}
