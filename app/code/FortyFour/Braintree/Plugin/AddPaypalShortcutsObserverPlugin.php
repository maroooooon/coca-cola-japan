<?php

namespace FortyFour\Braintree\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class AddPaypalShortcutsObserverPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * AddPaypalShortcutsObserverPlugin constructor.
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Braintree\Observer\AddPaypalShortcuts $subject
     * @param \Closure $proceed
     * @param Observer $observer
     * @return mixed
     */
    public function aroundExecute(
        \Magento\Braintree\Observer\AddPaypalShortcuts $subject,
        \Closure $proceed,
        Observer $observer
    ) {
        if ($this->isBraintreePayPalActive()) {
            return $proceed($observer);
        }
    }

    /**
     * @param null $store
     * @return bool
     */
    private function isBraintreePayPalActive($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'payment/braintree_paypal/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
