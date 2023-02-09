<?php

namespace FortyFour\LazySizes\Plugin;

use FortyFour\LazySizes\Helper\Config;
use Psr\Log\LoggerInterface;

abstract class AbstractPlugin
{
    /**
     * @var Config
     */
    protected $lazySizesConfig;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TemplatePlugin constructor.
     * @param Config $lazySizesConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $lazySizesConfig,
        LoggerInterface $logger
    ) {
        $this->lazySizesConfig = $lazySizesConfig;
        $this->logger = $logger;
    }
}
