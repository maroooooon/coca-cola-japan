<?php

namespace FortyFour\Sales\Plugin\Quote;

use Magento\Directory\Helper\Data;
use Magento\Directory\Model\AllowedCountries;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class SetDefaultCountry
{
    /**
     * @var Data
     */
    private $directoryHelper;
    /**
     * @var AllowedCountries
     */
    private $allowedCountries;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * SetDefaultCountry constructor.
     * @param Data $directoryHelper
     * @param AllowedCountries $allowedCountries
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $directoryHelper,
        AllowedCountries $allowedCountries,
        StoreManagerInterface $storeManager
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->allowedCountries = $allowedCountries;
        $this->storeManager = $storeManager;
    }

    public function afterAddProduct(Quote $subject, $result)
    {
        $defaultCountry = $this->directoryHelper->getDefaultCountry();
        $supportedCountries = $this->allowedCountries->getAllowedCountries(
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        // Is there a default country?
        if (!$defaultCountry) {
            return $result;
        }

        $shippingAddress = $subject->getShippingAddress();

        // Is there already a country ID?
        if ($shippingAddress->getCountryId()) {
            // But is the country ID even supported?
            if (in_array($shippingAddress->getCountryId(), $supportedCountries)) {
                return $result;
            }
        }

        // set country id on quote
        $shippingAddress->setCountryId($defaultCountry);

        return $result;
    }
}