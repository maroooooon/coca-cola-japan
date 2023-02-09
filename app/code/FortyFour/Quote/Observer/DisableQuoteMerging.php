<?php

namespace FortyFour\Quote\Observer;

use FortyFour\Quote\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;

class DisableQuoteMerging implements ObserverInterface
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
     * DisableQuoteMerging constructor.
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
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isCartMergingEnabled()) {
            /** @var Quote $quote */
            $quote = $observer->getEvent()->getQuote();
            $quote->removeAllItems();
        }
    }
}
