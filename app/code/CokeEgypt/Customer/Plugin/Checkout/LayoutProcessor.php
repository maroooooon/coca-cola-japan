<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEgypt\Customer\Plugin\Checkout;

use CokeEgypt\Customer\Helper\Config;
use Magento\Framework\Stdlib\ArrayManager;
class LayoutProcessor
{

    /**
     * @var Config
     */
    private $configHelper;
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param Config $configHelper
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        Config $configHelper,
        ArrayManager $arrayManager
    ) {
        $this->configHelper = $configHelper;
        $this->arrayManager = $arrayManager;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $jsLayout
    ) {
        // Skip if module is not enabled for this store
        if(!$this->configHelper->isEnabled()) return $jsLayout;

        $streetPaths = $this->arrayManager->findPaths('street', $jsLayout);
        foreach ($streetPaths as $path)
        {
            // Change street address 1 & 2 labels
            $jsLayout = $this->arrayManager->set($path . '/children/1/label', $jsLayout, __('Building Number'));
            $jsLayout = $this->arrayManager->set($path . '/children/2/label', $jsLayout, __('Apartment Number'));
            // Make street address 1 & 2 required
            $jsLayout = $this->arrayManager->set($path . '/children/1/validation/required-entry', $jsLayout, true);
            $jsLayout = $this->arrayManager->set($path . '/children/2/validation/required-entry', $jsLayout, true);
        }


        return $jsLayout;
    }
}
