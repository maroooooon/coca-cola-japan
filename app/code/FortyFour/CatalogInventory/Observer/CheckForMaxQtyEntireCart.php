<?php

namespace FortyFour\CatalogInventory\Observer;

use FortyFour\CatalogInventory\Helper\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Setup\Exception;
use Psr\Log\LoggerInterface;

class CheckForMaxQtyEntireCart implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var Config
     */
    private $config;

    /**
     * CheckForMaxQtyEntireCart constructor.
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        Config $config
    ) {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return false|CheckForMaxQtyEntireCart
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if (!($maxQtyAllowedForCart = $this->config->getMaxQtyAllowedForEntireCart())) {
            return $this;
        }

        /* @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getItemsQty() > $maxQtyAllowedForCart) {
            $this->messageManager->addErrorMessage(
                __('Sorry, you are allowed to have only %1 items in total in your cart.', $maxQtyAllowedForCart)
            );
            throw new \Exception(__('We can\'t add this item to your shopping cart right now.'));
        }
    }
}
