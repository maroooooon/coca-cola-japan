<?php

namespace FortyFour\DataLayer\Observer;

use FortyFour\DataLayer\Helper\Config;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddBrandToCollection implements ObserverInterface
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
     * AddBrandToCollection constructor.
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
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->config->isBrandDataLayerEnabled()) {
            return $this;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $observer->getEvent()->getCollection();
        $productCollection->addAttributeToSelect('brand');

        return $this;
    }
}
