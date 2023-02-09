<?php

namespace Coke\PricesWithoutDecimals\Plugin\Model;

use Coke\PricesWithoutDecimals\Helper\Config;
use Coke\PricesWithoutDecimals\Service\PricingFormatter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class PriceCurrencyPlugin
{
    const DEFAULT_PRECISION = 2;

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

    public function beforeFormat(\Magento\Directory\Model\PriceCurrency $subject, $amount, $includeContainer = true, $precision = self::DEFAULT_PRECISION, $scope = null, $currency = null)
    {
        if (!$this->configHelper->isShowingDecimals()) {
            return [
                $this->pricingFormatter->floorCents($amount),
                $includeContainer,
                $this->configHelper::PRECISION,
                $scope,
                $currency
            ];
        }
    }
}
