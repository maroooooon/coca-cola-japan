<?php

namespace Coke\Whitelist\Model\Checkout;

use Coke\Whitelist\Model\ModuleConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ModuleConfig
     */
    private $config;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @param LoggerInterface $logger
     * @param ModuleConfig $config
     */
    public function __construct(
        LoggerInterface $logger,
        ModuleConfig $config,
        Session $checkoutSession
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array|array[]
     */
    public function getConfig(): array
    {
        if (!($this->config->canShowWhitelistReviewDisclaimer()) || !($quote = $this->getQuote())) {
            return [];
        }

        return [
            'whitelist' => [
                'whitelist_status_pending' => $quote->getData('whitelist_status_pending')
            ]
        ];
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote|null
     */
    private function getQuote()
    {
        try {
            return $this->checkoutSession->getQuote();
        } catch (NoSuchEntityException | LocalizedException $e) {
            return null;
        }
    }
}
