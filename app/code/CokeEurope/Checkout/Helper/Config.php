<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\Checkout\Helper;

use CokeEurope\StoreModifications\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManager;

class Config extends AbstractHelper
{
    private StoreManager $storeManager;
    private Data $data;

    public function __construct(Context $context, StoreManager $storeManager, Data $data)
    {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->data = $data;
    }

    /**
     * @return float
     */
    public function getFreeShippingSubtotal()
    {
        return $this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * This function checks if the current website is the UK website
     *
     * @return bool The website ID of the UK website.
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isUkWebsite(): bool
    {
        return $this->storeManager->getWebsite()->getId() === $this->data->getUkWebsite()->getId();
    }

    /**
     * Returns true if the current website is the Europe or UK website
     *
     * @return bool A boolean value.
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isEuropeUKWebsite(): bool
    {
        return $this->isUkWebsite() || $this->storeManager->getWebsite()->getId() === $this->data->getEuropeWebsite()->getId();
    }
}

