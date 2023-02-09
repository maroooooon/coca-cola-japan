<?php

namespace FortyFour\FlatRateExtended\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_FLAT_RATE_PRICE_BY_COUNTRY = 'carriers/flatrate/price_by_country';

    /**
     * @var Json
     */
    private $json;

    /**
     * Config constructor.
     * @param Context $context
     * @param Json $json
     */
    public function __construct(
        Context $context,
        Json $json
    ) {
        parent::__construct($context);
        $this->json = $json;
    }

    /**
     * @param null $store
     * @return array|null
     */
    public function getPriceByCountryMap($store = null): ?array
    {
        $priceByCountry = $this->scopeConfig->getValue(
            self::XML_PATH_FLAT_RATE_PRICE_BY_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $priceByCountry ? $this->json->unserialize($priceByCountry) : null;
    }

    /**
     * @param string $countryId
     * @param null $store
     * @return mixed|null
     */
    public function getPriceByCountry(string $countryId, $store = null)
    {
        if (!$this->getPriceByCountryMap($store)) {
            return null;
        }

        foreach ($this->getPriceByCountryMap() as $item) {
            if ($item['country'] == $countryId) {
                return $item['price'];
            }
        }

        return null;
    }
}
