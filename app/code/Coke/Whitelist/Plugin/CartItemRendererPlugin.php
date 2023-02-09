<?php

namespace Coke\Whitelist\Plugin;

use Coke\Whitelist\Model\ModuleConfig;
use Psr\Log\LoggerInterface;

class CartItemRendererPlugin
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
     * @param LoggerInterface $logger
     * @param ModuleConfig $config
     */
    public function __construct(
        LoggerInterface $logger,
        ModuleConfig $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Item\Renderer $subject
     * @param $template
     * @return array
     */
    public function beforeSetTemplate(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $template)
    {
        if (!$this->config->canShowWhitelistItemStatus()) {
            return [$template];
        }

        $template = "Coke_Whitelist::cart/item/default.phtml";
        return [$template];
    }
}
