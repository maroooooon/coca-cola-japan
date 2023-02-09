<?php

namespace CokeEurope\NorthernIrelandVerification\Model\Validator;

use CokeEurope\AddressAutocomplete\Helper\Config;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddressVerification implements ValidatorInterface
{
    /** @var Config  */
    protected Config $config;

    /** @var StoreManagerInterface  */
    protected StoreManagerInterface $storeManager;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param AbstractAddress $address
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(AbstractAddress $address)
    {
        $errors = [];

        if ($this->storeManager->getWebsite()->getCode() !== 'coke_uk') {
            return $errors;
        }

        $isNorthernIreland = $this->storeManager->getStore()->getCode() === 'northern_ireland_english';
        $isNIAddress = strpos($address->getPostcode(), 'BT') === 0;

        if ($isNorthernIreland !== $isNIAddress && $address->getAddressType() !== 'billing') {
            $errors[] = __('Unable to save address. Only addresses in region %1 can be saved to the address book.', $this->storeManager->getGroup()->getName());
        }

        return $errors;
    }
}
