<?php

namespace Coke\PricesWithoutDecimals\Plugin\Model;

use Coke\PricesWithoutDecimals\Helper\Config;
use Coke\PricesWithoutDecimals\Service\PricingFormatter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class CurrencyPlugin
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

    public function beforeFormatPrecision(\Magento\Directory\Model\Currency $subject, $price, $precision, $options = [], $includeContainer = true, $addBrackets = false)
    {
        if (!$this->configHelper->isShowingDecimals()) {
            return [
                $this->pricingFormatter->floorCents($price),
                $this->configHelper::PRECISION,
                $options,
                $includeContainer,
                $addBrackets
            ];
        }
    }
}
