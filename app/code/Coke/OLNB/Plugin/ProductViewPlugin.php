<?php

namespace Coke\OLNB\Plugin;

use Coke\OLNB\Helper\Config;
use Psr\Log\LoggerInterface;

class ProductViewPlugin
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
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param $result
     */
    public function afterShouldRenderQuantity(
        \Magento\Catalog\Block\Product\View $subject,
        $result
    ) {
        if (!$this->config->shouldHideQtyInputOnPdp()) {
            return $result;
        }

        return false;
    }
}
