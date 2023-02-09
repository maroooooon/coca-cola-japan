<?php

namespace FortyFour\Cds\Plugin;

use FortyFour\Cds\Helper\Config;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class CustomerExtractorPlugin
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
     * ConsentApiPlugin constructor.
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
     * @param \Coke\Cds\Model\CustomerExtractor $subject
     * @param $result
     * @param RequestInterface $request
     */
    public function afterExtract(
        \Coke\Cds\Model\CustomerExtractor $subject,
        $result,
        RequestInterface $request
    ) {
        if ($this->config->isNewsletterSubscriptionDisabled()) {
            $result->getExtensionAttributes()->setIsSubscribed(0);
        }

        return $result;
    }
}
