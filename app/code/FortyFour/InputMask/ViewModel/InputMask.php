<?php

namespace FortyFour\InputMask\ViewModel;

use FortyFour\InputMask\Helper\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class InputMask implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
