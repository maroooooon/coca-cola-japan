<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CokeEurope\Tax\Observer;

use CokeEurope\Tax\Helper\Config;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item;
use Magento\Sales\Model\Order\Item as OrderItem;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
	protected Copy $objectCopyService;
    private Config $taxConfig;

	public function __construct(
        Copy $objectCopyService,
        Config $taxConfig
    )
	{
		$this->objectCopyService = $objectCopyService;
        $this->taxConfig = $taxConfig;
	}

	/**
	 * @param Observer $observer
	 */
	public function execute(Observer $observer)
	{
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $sugarTaxTotal = (float) $quote->getSugarTaxTotal();
        $quote->setData('sugar_tax_total', $sugarTaxTotal);

        $this->objectCopyService->copyFieldsetToTarget('sales_convert_quote', 'to_order', $quote, $order);
        return $this;
	}
}
