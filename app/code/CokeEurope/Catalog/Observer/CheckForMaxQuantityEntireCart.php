<?php

namespace CokeEurope\Catalog\Observer;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote;

class CheckForMaxQuantityEntireCart implements ObserverInterface
{
	private ManagerInterface $messageManager;
	private Config $config;
	
	/**
	 * CheckForMaxQtyEntireCart constructor.
	 * @param ManagerInterface $messageManager
	 * @param Config $config
	 */
	public function __construct(
		ManagerInterface $messageManager,
		Config $config
	) {
		$this->messageManager = $messageManager;
		$this->config = $config;
	}
	
	/**
	 * It checks if the module is enabled, and if it is, it checks if the number of items in the cart is greater than the
	 * maximum allowed, and if it is, it throws an exception
	 *
	 * @param Observer $observer The observer object.
	 */
	public function execute(Observer $observer)
	{
		if (!$this->config->getCartMaximumIsEnabled()) {
			return $this;
		}
		
		/* @var Quote $quote */
		$quote = $observer->getEvent()->getQuote();
		$maxQtyAllowedForCart = $this->config->getCartMaximumAmount();
		if ($quote->getItemsQty() > $maxQtyAllowedForCart) {
			$this->messageManager->addErrorMessage(
				__('Sorry, you are allowed to have only %1 items in total in your cart.', $maxQtyAllowedForCart)
			);
			throw new \Exception(__('Sorry, you are allowed to have only %1 items in total in your cart.', $maxQtyAllowedForCart));
		}
	}
}
