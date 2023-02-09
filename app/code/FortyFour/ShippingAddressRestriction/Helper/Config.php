<?php

namespace FortyFour\ShippingAddressRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_SHIP_ADDRESS_RESTRICTION_ENABLED = 'shipping_address_restriction/general/enabled';
    const XML_PATH_SHIP_ADDRESS_RESTRICTION_APPLY_TO_REGION =
        'shipping_address_restriction/city_region/apply_to_region';
    const XML_PATH_SHIP_ADDRESS_RESTRICTION_ALLOWED_CITY_REGION =
        'shipping_address_restriction/city_region/allowed_city_region_list';
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
     * @return bool
     */
    public function isEnabled($store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHIP_ADDRESS_RESTRICTION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canApplyToRegion($store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHIP_ADDRESS_RESTRICTION_APPLY_TO_REGION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return array|null
     */
    public function getAllowedCityRegionList($store = null): ?array
    {
        $cityRegionList = $this->scopeConfig->getValue(
            self::XML_PATH_SHIP_ADDRESS_RESTRICTION_ALLOWED_CITY_REGION,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $cityRegionList ? $this->json->unserialize($cityRegionList) : null;
    }

    /**
     * @param string $city
     * @param null $store
     * @return false|string[]|null
     */
    public function getCityRegionListByCity(string $city, $store = null)
    {
        $cityRegionItems = $this->getAllowedCityRegionList($store);

        foreach ($cityRegionItems as $cityRegionItem) {
            if ($cityRegionItem['city'] == $city) {
                return explode(PHP_EOL, $cityRegionItem['region']);
            }
        }

        return null;
    }

    /**
     * @param null $store
     * @return array|null
     */
    public function getCityList($store = null): ?array
    {
        $cityRegionItems = $this->getAllowedCityRegionList($store);
        $cities = [];

        foreach ($cityRegionItems as $cityRegionItem) {
            $cities[] = $cityRegionItem['city'];
        }

        return $cities;
    }
}
