<?php

/*
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author tanya.lamontagne
 */

namespace CokeEurope\Currency\ViewModel;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CurrencyViewModel implements ArgumentInterface
{
    private CurrencyFactory $currencyFactory;

    /**
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(CurrencyFactory $currencyFactory)
    {
        $this->currencyFactory = $currencyFactory;
    }


    public function getCurrencySymbol(string $code): string
    {
        $currency = $this->currencyFactory->create()->load($code);
        return $currency->getCurrencySymbol();
    }
}
