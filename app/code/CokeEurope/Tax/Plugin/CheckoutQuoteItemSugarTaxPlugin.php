<?php
/**
 * CheckoutQuoteItemStatusPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Tax\Plugin;

use CokeEurope\Tax\Helper\Config as TaxConfig;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;

class CheckoutQuoteItemSugarTaxPlugin
{
    private TaxConfig $taxConfig;

    /**
     * @param TaxConfig $taxConfig
     */
    public function __construct(TaxConfig $taxConfig)
    {
        $this->taxConfig = $taxConfig;
    }

    /**
     * It adds a new field to the item data array to be used in the frontend js
     *
     * @param AbstractItem $subject The object that called the method.
     * @param array $result The result of the method.
     * @param Item $item The item object
     *
     * @return array The result of the method.
     */
    public function afterGetItemData(AbstractItem $subject, $result, Item $item): array
    {
        if (!$this->taxConfig->isEnabled()){
            $result['sugar_tax'] = 0.00;
            return $result;
        }

        $result['sugar_tax'] = (float) $this->taxConfig->getItemSugarTax($item);
        return $result;
    }
}
