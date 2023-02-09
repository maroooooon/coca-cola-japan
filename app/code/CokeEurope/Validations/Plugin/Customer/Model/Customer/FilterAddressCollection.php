<?php

namespace CokeEurope\Validations\Plugin\Customer\Model\Customer;

use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;

class FilterAddressCollection
{
    /** @var RequestInterface  */
    protected $request;

    /** @var  */
    protected $allowedCountries;

    public function __construct(
        RequestInterface $request,
        AllowedCountries $allowedCountries
    ) {
        $this->request = $request;
        $this->allowedCountries = $allowedCountries;
    }

    /**
     *
     *
     * @param $subject
     * @param $collection
     * @return \Magento\Customer\Model\ResourceModel\Address\Collection
     */
    public function afterGetAddressCollection($subject, $collection)
    {
        if ($this->request->getModuleName() !== 'checkout') {
            return $collection;  //There's no need to alter this
        }

        $allowedCountries = $this->allowedCountries->getAllowedCountries(ScopeInterface::SCOPE_STORES);

        /** @var \Magento\Customer\Model\ResourceModel\Address\Collection $collection */
        $collection->addFieldToFilter('country_id', ['in' => $allowedCountries]);

        return $collection;
    }
}
