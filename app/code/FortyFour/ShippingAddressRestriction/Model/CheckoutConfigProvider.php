<?php

namespace FortyFour\ShippingAddressRestriction\Model;

use FortyFour\ShippingAddressRestriction\Helper\Config as ShippingAddressRestrictionConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Psr\Log\LoggerInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
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
     * CheckoutConfigProvider constructor.
     * @param LoggerInterface $logger
     * @param ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig
     */
    public function __construct(
        LoggerInterface $logger,
        ShippingAddressRestrictionConfig $shippingAddressRestrictionConfig
    ) {
        $this->logger = $logger;
        $this->shippingAddressRestrictionConfig = $shippingAddressRestrictionConfig;
    }

    /**
     * @return array|array[]
     */
    public function getConfig()
    {
        if (!$this->shippingAddressRestrictionConfig->isEnabled()) {
            return [];
        }

        $data = [];
        $cities = $this->shippingAddressRestrictionConfig->getCityList();
        foreach ($cities as $city) {
            $data[] = [
                'value' => $city,
                'label' => $city,
                'disable' => 0
            ];
        }

        try {
            return [
                'shipping_address_restriction' => [
                    'city' => $data
                ]
            ];
        } catch (\Exception $e) {
            $this->logger->debug(
                __(
                    '[\FortyFour\ShippingAddressRestriction\Model\CheckoutConfigProvider::getConfig] %1',
                    $e->getMessage()
                )
            );
            return [];
        }
    }
}
