<?php

namespace CokeEurope\Catalog\Observer;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;

class CatalogAfterAddToCartObserver implements ObserverInterface
{
	private Config $configHelper;
	private Http $request;

	public function __construct(Config $configHelper, Http $request)
	{
		$this->configHelper = $configHelper;
		$this->request = $request;
	}

	/**
	 * If the module is enabled, set the moderation status of the quote item to the value of the `pending_approval` request
	 * parameter
	 *
	 * @param Observer $observer The observer object.
	 */
	public function execute(Observer $observer)
	{
        /** @var Item $quoteItem */
        $quoteItem = $observer->getEvent()->getData('quote_item');
        if ($this->configHelper->getModerationEnabled() && $quoteItem->getSku() == $this->configHelper->getConfigurableSku()){
			$quoteItem->setModerationStatus($this->request->getParam('pending_approval'));
		}
	}
}
