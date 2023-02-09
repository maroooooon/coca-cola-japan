<?php

namespace Coke\Sarp2\Helper;

use Aheadworks\Sarp2\Model\Quote\Item\Checker\IsSubscription;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class ForceLogin
{
    const URL_VARIABLE = '{{url}}';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var IsSubscription
     */
    private $isSubscription;
    /**
     * @var Context
     */
    private $httpContext;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param Session $checkoutSession
     * @param IsSubscription $isSubscription
     * @param Context $httpContext
     * @param CustomerSession $customerSession
     * @param UrlInterface $url
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        Session $checkoutSession,
        IsSubscription $isSubscription,
        Context $httpContext,
        CustomerSession $customerSession,
        UrlInterface $url
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->isSubscription = $isSubscription;
        $this->httpContext = $httpContext;
        $this->customerSession = $customerSession;
        $this->url = $url;
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canProceedToCheckout($store = null): bool
    {
        if (!$this->config->isForceLoginEnabled($store) || $this->isCustomerLoggedIn()) {
            return true;
        }

        return !$this->checkForSubscriptionProduct();
    }

    /**
     * @param null $store
     * @return string
     */
    public function renderForceLoginMessage($store = null): string
    {
        $find = [self::URL_VARIABLE];
        $replace = [$this->url->getBaseUrl()];

        return str_replace($find, $replace, $this->config->getForceLoginMessage($store));
    }

    /**
     * @return bool
     */
    private function checkForSubscriptionProduct(): bool
    {
        try {
            $quote = $this->checkoutSession->getQuote();
            if ($quote->getId()) {
                foreach ($quote->getAllVisibleItems() as $item) {
                    if ($this->isSubscription->check($item)) {
                        return true;
                    }
                }
            }
        } catch (LocalizedException $e) {
            return false;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isCustomerLoggedIn(): bool
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)
            || $this->customerSession->isLoggedIn();
    }
}
