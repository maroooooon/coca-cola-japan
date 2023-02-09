<?php

namespace Coke\Customer\Plugin;

use Magento\Customer\Block\Form\Register;
use Magento\Framework\Module\Manager;
use Magento\Newsletter\Model\Config;
use Magento\Store\Model\ScopeInterface;

class FixNewsletterActiveCheck
{
    /**
     * @var Manager
     */
    private $moduleManager;
    /**
     * @var Config
     */
    private $newsLetterConfig;

    public function __construct(
        Manager $moduleManager,
        Config $newsLetterConfig
    )
    {
        $this->moduleManager = $moduleManager;
        $this->newsLetterConfig = $newsLetterConfig;
    }
    public function aroundIsNewsletterEnabled(Register $subject, callable $proceed)
    {
        return $this->moduleManager->isOutputEnabled('Magento_Newsletter')
            && $this->newsLetterConfig->isActive(ScopeInterface::SCOPE_STORES);
    }
}
