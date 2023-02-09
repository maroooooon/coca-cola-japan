<?php

namespace CokeEurope\Tax\Plugin;

use CokeEurope\Tax\Helper\Config;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository\SaveHandler;
use Magento\Checkout\Model\Session;

class SugarTaxSaveQuotePlugin
{
    private Config $taxConfig;
    private Session $checkoutSession;

    public function __construct(
        Config $taxConfig,
        Session $checkoutSession
    ) {
        $this->taxConfig = $taxConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param SaveHandler $subject
     * @param CartItemInterface $quote
     * @return array
     */
    public function beforeSave(SaveHandler $subject, CartInterface $quote): array
    {
        if (!$this->taxConfig->isEnabled()){
            $quote->setSugarTaxTotal(0.00);
            return [$quote];
        }

        $quote->setSugarTaxTotal((float)$this->taxConfig->getTotalItemsSugarTaxForQuote($quote));
        return [$quote];
    }
}
