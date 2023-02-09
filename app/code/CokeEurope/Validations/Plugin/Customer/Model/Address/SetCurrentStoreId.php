<?php

namespace CokeEurope\Validations\Plugin\Customer\Model\Address;

use Magento\Store\Model\StoreManagerInterface;

class SetCurrentStoreId
{
    /** @var StoreManagerInterface  */
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param $address
     * @return void
     */
    public function beforeValidate($address)
    {
        $currentWebsiteCode = $this->storeManager->getWebsite()->getCode();
        $allowedWebsites = ['coke_uk', 'coke_eu'];

        if (!in_array($currentWebsiteCode, $allowedWebsites)) {
            return;
        }
        $address->setStoreId($this->storeManager->getStore()->getId()); // Reset to current store
    }
}
