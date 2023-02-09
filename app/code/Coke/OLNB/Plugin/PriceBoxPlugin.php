<?php

namespace Coke\OLNB\Plugin;

use Coke\OLNB\Helper\Config;
use Psr\Log\LoggerInterface;

class PriceBoxPlugin
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
     * ProductViewPlugin constructor.
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
     * @param \Magento\Framework\Pricing\Render\PriceBox $subject
     * @param $result
     * @return false
     */
    public function afterToHtml(
        \Magento\Framework\Pricing\Render\PriceBox $subject,
        $result
    ) {
        if (!$this->config->shouldHidePricetOnPdp()) {
            return $result;
        }

        return false;
    }
}
