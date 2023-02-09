<?php

namespace FortyFour\Store\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreSwitcherInterface;
use Psr\Log\LoggerInterface;

class SetStoreCookie implements ObserverInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var StoreSwitcherInterface
     */
    private $storeSwitcher;
    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * ContextPlugin constructor.
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param StoreSwitcherInterface $storeSwitcher
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        StoreSwitcherInterface $storeSwitcher,
        CookieManagerInterface $cookieManager
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->storeSwitcher = $storeSwitcher;
        $this->cookieManager = $cookieManager;
    }

    /**
     * Set store cookie
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $storeCookie = $this->cookieManager->getCookie('store');
            $store = $this->storeManager->getStore();

            if ($storeCookie != $store->getCode()) {
                $this->storeSwitcher->switch(
                    $store,
                    $store,
                    $store->getCurrentUrl()
                );
                $this->logger->info(__('[FortyFour\Store\Observer\SetStoreCookie] set the store cookie.'));
            }
        } catch (\Exception $e) {
            $this->logger->error(__('[FortyFour\Store\Observer\SetStoreCookie] %1', $e->getMessage()));
        }
    }
}
