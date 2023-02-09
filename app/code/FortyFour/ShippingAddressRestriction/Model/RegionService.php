<?php

namespace FortyFour\ShippingAddressRestriction\Model;

use FortyFour\ShippingAddressRestriction\Api\Data\RegionServiceResponseInterfaceFactory;
use FortyFour\ShippingAddressRestriction\Api\RegionServiceInterface;
use FortyFour\ShippingAddressRestriction\Helper\Config as ShippingAddressRestrictionConfig;
use Psr\Log\LoggerInterface;

class RegionService implements RegionServiceInterface
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
     * @var RegionServiceResponseInterfaceFactory
     */
    private $regionServiceResponseInterfaceFactory;

    /**
     * RegionService constructor.
     * @param LoggerInterface $logger
     * @param ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig
     * @param RegionServiceResponseInterfaceFactory $regionServiceResponseInterfaceFactory
     */
    public function __construct(
        LoggerInterface $logger,
        ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig,
        RegionServiceResponseInterfaceFactory $regionServiceResponseInterfaceFactory
    ) {
        $this->logger = $logger;
        $this->shippingAddressRestrictionConfig = $shippingAddressRestrictionConfig;
        $this->regionServiceResponseInterfaceFactory = $regionServiceResponseInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function getRegionsByCity(string $city)
    {
        $data = [];
        $regions = $this->shippingAddressRestrictionConfig->getCityRegionListByCity($city);
        foreach ($regions as $region) {
            $regionResponse = $this->regionServiceResponseInterfaceFactory->create();
            $region = trim($region);
            $regionResponse->setLabel($region)
                ->setValue($region)
                ->setDisable(0);
            $data[] = $regionResponse;
        }

        return $data;
    }
}
