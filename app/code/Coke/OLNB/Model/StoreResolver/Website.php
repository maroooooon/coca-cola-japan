<?php

namespace Coke\OLNB\Model\StoreResolver;

use Magento\Framework\Exception\NoSuchEntityException;

class Website extends \Magento\Store\Model\StoreResolver\Website
{
    public function getAllowedStoreIds($scopeCode)
    {
        $scopes = ['olnb_gb', 'olnb_ni', 'olnb_eu', 'olnb_norway', 'olnb_turkey'];
        $result = parent::getAllowedStoreIds($scopeCode);

        foreach ($scopes as $scope) {
            if ($scopeCode === $scope) {
                continue;
            }

            try {
                $result = array_merge($result, parent::getAllowedStoreIds($scope));
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        return $result;
    }
}