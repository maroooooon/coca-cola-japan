<?php

namespace FortyFour\DataLayer\Plugin\Block;

use FortyFour\DataLayer\Helper\Config;
use Psr\Log\LoggerInterface;

class AbstractPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Config
     */
    protected $config;

    /**
     * ListProductPlugin constructor.
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
}
