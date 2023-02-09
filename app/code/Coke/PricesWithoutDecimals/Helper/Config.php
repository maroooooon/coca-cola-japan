<?php

namespace Coke\PricesWithoutDecimals\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const PRECISION = 0;
    const XML_SHOW_DECIMALS = 'coke_catalog/pricing/show_decimals';

    public function isShowingDecimals()
    {
        return $this->scopeConfig->isSetFlag(self::XML_SHOW_DECIMALS, ScopeInterface::SCOPE_WEBSITE);
    }
}
