<?php

namespace Coke\PricesWithoutDecimals\Plugin\Pricing\Render;

use Coke\PricesWithoutDecimals\Helper\Config;
use Coke\PricesWithoutDecimals\Service\PricingFormatter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class AmountPlugin
{
    /**
     * @var PricingFormatter
     */
    private $pricingFormatter;
    /**
     * @var Config
     */
    private $configHelper;

    public function __construct(
        PricingFormatter $pricingFormatter,
        Config $configHelper
    ){

        $this->pricingFormatter = $pricingFormatter;
        $this->configHelper = $configHelper;
    }

    public function beforeFormatCurrency(\Magento\Framework\Pricing\Render\Amount $subject, $amount, $includeContainer = true, $precision = PriceCurrencyInterface::DEFAULT_PRECISION)
    {
        if (!$this->configHelper->isShowingDecimals()) {
            return [
                $this->pricingFormatter->floorCents($amount),
                $includeContainer,
                $this->configHelper::PRECISION
            ];
        }
    }
}
