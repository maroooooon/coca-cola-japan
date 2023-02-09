<?php

namespace FortyFour\ShippingAddressRestriction\Model;

use FortyFour\ShippingAddressRestriction\Api\CityServiceInterface;
use FortyFour\ShippingAddressRestriction\Api\Data\CityServiceResponseInterfaceFactory;
use FortyFour\ShippingAddressRestriction\Helper\Config as ShippingAddressRestrictionConfig;
use Psr\Log\LoggerInterface;

class CityService implements CityServiceInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ShippingAddressRestrictionConfig
     */
    private $shippingAddressRestrictionConfig;
    /**
     * @var CityServiceResponseInterfaceFactory
     */
    private $cityServiceResponseInterfaceFactory;

    /**
     * CityService constructor.
     * @param LoggerInterface $logger
     * @param ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig
     * @param CityServiceResponseInterfaceFactory $cityServiceResponseInterfaceFactory
     */
    public function __construct(
        LoggerInterface $logger,
        ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig,
        CityServiceResponseInterfaceFactory $cityServiceResponseInterfaceFactory
    ) {
        $this->logger = $logger;
        $this->shippingAddressRestrictionConfig = $shippingAddressRestrictionConfig;
        $this->cityServiceResponseInterfaceFactory = $cityServiceResponseInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function getCities()
    {
        $data = [];
        $cities = $this->shippingAddressRestrictionConfig->getCityList();
        foreach ($cities as $city) {
            $cityResponse = $this->cityServiceResponseInterfaceFactory->create();
            $region = trim($city);
            $cityResponse->setLabel($city)
                ->setValue($city)
                ->setDisable(0);
            $data[] = $cityResponse;
        }

        return $data;
    }
}
