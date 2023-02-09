<?php

namespace CokeEurope\Tax\Plugin;

use Magento\Checkout\Block\Cart\LayoutProcessor;
use CokeEurope\Tax\Helper\Config;

class ShowSugarTaxProviderPlugin
{
    private Config $taxConfig;

    public function __construct(Config $taxConfig)
    {
        $this->taxConfig = $taxConfig;
    }

    /**
     * If tax is disabled, set the componentDisabled flag to true
     * @param LayoutProcessor $processor The LayoutProcessor object
     * @param array $jsLayout The entire layout array.
     *
     * @return The $jsLayout variable is being returned.
     */
    public function afterProcess(
        LayoutProcessor $processor,
        array $jsLayout
    )
    {
        if (!$this->taxConfig->isEnabled()) {
            $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]["totals"]["children"]["sugar_tax"]["config"]["componentDisabled"] = true;
        }

        return $jsLayout;
    }

}
