<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CokeEurope\Catalog\Observer;

use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item;
use Magento\Sales\Model\Order\Item as OrderItem;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
	protected Copy $objectCopyService;
	
	private ItemFactory $quoteItemFactory;
	
	private Item $itemResourceModel;
	
	/**
	 * @param Copy $objectCopyService
	 * @param ItemFactory $quoteItemFactory
	 */
	public function __construct(
		Copy        $objectCopyService,
		ItemFactory $quoteItemFactory,
		Item        $itemResourceModel
	)
	{
		$this->objectCopyService = $objectCopyService;
		$this->quoteItemFactory = $quoteItemFactory;
		$this->itemResourceModel = $itemResourceModel;
	}
	
	/**
	 * @param Observer $observer
	 */
	public function execute(Observer $observer)
	{
		/* @var OrderItem $order */
		$order = $observer->getEvent()->getData('order');
		
		/** @var OrderItem $orderItem */
		foreach ($order->getItems() as $orderItem) {
			$quoteItem = $this->quoteItemFactory->create();
			$this->itemResourceModel->load($quoteItem, $orderItem->getQuoteItemId());
			
			if ($quoteItem->getModerationStatus() !== null) {
				$orderItem->setModerationStatus($quoteItem->getModerationStatus());
				$this->objectCopyService->copyFieldsetToTarget('sales_convert_quote', 'to_order_item', $quoteItem,
					$orderItem);
			}
		}
		return $this;
	}
}
