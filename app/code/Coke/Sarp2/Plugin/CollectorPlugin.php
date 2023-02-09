<?php

namespace Coke\Sarp2\Plugin;

use Aheadworks\Sarp2\Model\Quote\Substitute\Quote\Address as AddressSubstitute;
use Aheadworks\Sarp2\Model\Sales\Total\Profile\Collector\Shipping\RatesCollector;
use Coke\Sarp2\Helper\Config;
use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface;

class CollectorPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;

    /**
     * ProfileAddressToOrderPlugin constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param RatesCollector $subject
     * @param $result
     * @param Address|AddressSubstitute $address
     * @return array|mixed
     */
    public function afterCollect(
        RatesCollector $subject,
        $result,
        $address
    ) {
        if ($this->config->isFreeShippingForSubscriptionsEnabled()) {
            foreach ($result as $rate) {
                $rate['price'] = 0.00;
            }
        }

        return $result;
    }
}
