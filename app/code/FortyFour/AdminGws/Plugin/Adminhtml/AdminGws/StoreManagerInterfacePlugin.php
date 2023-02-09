<?php

namespace FortyFour\AdminGws\Plugin\Adminhtml\AdminGws;

use Magento\Store\Model\StoreManagerInterface;

class StoreManagerInterfacePlugin extends AbstractAdminGwsPlugin
{
    /**
     * @param StoreManagerInterface $subject
     * @param $result
     * @param false $withDefault
     * @param false $codeKey
     * @return mixed
     */
    public function afterGetStores(
        StoreManagerInterface $subject,
        $result,
        $withDefault = false,
        $codeKey = false
    ) {
        if ((strpos($this->request->getFullActionName(), 'catalog_product_attribute_') !== false)
            && ($adminUser = $this->getAdminUser())
            && $adminUser->getData('product_attribute_access') == 1) {
            return $this->getStores();
        }

        return $result;
    }
}
