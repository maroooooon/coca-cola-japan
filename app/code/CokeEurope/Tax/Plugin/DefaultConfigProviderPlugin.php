<?php

namespace CokeEurope\Tax\Plugin;

use CokeEurope\Tax\Helper\Config as TaxConfig;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class DefaultConfigProviderPlugin
{
    protected CheckoutSession $checkoutSession;
    private TaxConfig $taxConfig;

    public function __construct(
        CheckoutSession $checkoutSession,
        TaxConfig $taxConfig
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->taxConfig = $taxConfig;
    }

    /**
     * We're adding a new key to the `quoteItemData` array in the `totalsData` array in the `DefaultConfigProvider` class
     *
     * @param DefaultConfigProvider $subject The class that is being observed.
     * @param array $result The result of the original method.
     *
     * @return array The result of the original method, with the addition of the `sugar_tax` key.
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetConfig(
        DefaultConfigProvider $subject,
        array $result
    ): array
    {
        if (!$this->taxConfig->isEnabled()){
            $result['totalsData']['sugar_tax_total'] = 0.00;
            return $result;
        }

        $result['totalsData']['sugar_tax_total'] = (float) $result['quoteData']['sugar_tax_total'];
        return $result;
    }
}
