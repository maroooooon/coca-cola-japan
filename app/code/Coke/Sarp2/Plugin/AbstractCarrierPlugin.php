<?php

namespace Coke\Sarp2\Plugin;

use Coke\Sarp2\Helper\Config;
use Coke\Sarp2\Helper\SubscriptionChecker;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Psr\Log\LoggerInterface;

class AbstractCarrierPlugin
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var SubscriptionChecker
     */
    private $subscriptionChecker;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Config $config
     * @param SubscriptionChecker $subscriptionChecker
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        SubscriptionChecker $subscriptionChecker,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->subscriptionChecker = $subscriptionChecker;
        $this->logger = $logger;
    }

    /**
     * @param AbstractCarrier $subject
     * @param $result
     * @param $cost
     * @return float|mixed|string
     */
    public function afterGetFinalPriceWithHandlingFee(
        AbstractCarrier $subject,
        $result,
        $cost
    ) {
        if ($this->config->isFreeShippingForSubscriptionsEnabled()) {
            $this->logger->info(__('[AbstractCarrierPlugin] Free Shipping enabled.'));
            if (!$quoteId = $this->subscriptionChecker->getQuoteId()) { // No quote id - subscription
                $this->logger->info(__('[AbstractCarrierPlugin] No Quote Id.'));
                return 0.00;
            }

            if ($this->subscriptionChecker->isSubscription($quoteId)) { // Subscription found
                $this->logger->info(__('[AbstractCarrierPlugin] Sub found.'));
                return 0.00;
            }
        }

        return $result;
    }
}
