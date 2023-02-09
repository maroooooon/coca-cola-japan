<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bounteous\MaintenanceMode\Observer\Frontend\Controller;

use Bounteous\MaintenanceMode\Helper\Config;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class ActionPredispatch implements ObserverInterface
{
    /**
     * @var Config
     */
    protected Config $configHelper;

    /**
     * @var Http
     */
    protected Http $httpRedirect;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlInterface;

    /**
     * @param Config       $configHelper
     * @param Http         $httpRedirect
     * @param UrlInterface $urlInterface
     */
    public function __construct(Config $configHelper, Http $httpRedirect, UrlInterface $urlInterface)
    {
        $this->configHelper = $configHelper;
        $this->httpRedirect = $httpRedirect;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        // Redirect to /site-maintenance if enabled
        if (!$this->configHelper->isEnabled() || !$this->configHelper->getCmsPage()) {
            return;
        }

        $destination = $this->configHelper->getCmsPage();

        if (str_replace($this->urlInterface->getBaseUrl(), '', $this->urlInterface->getCurrentUrl()) !== $destination) {
            $this->httpRedirect->setRedirect('/' . $destination, 301);
        }
    }
}
