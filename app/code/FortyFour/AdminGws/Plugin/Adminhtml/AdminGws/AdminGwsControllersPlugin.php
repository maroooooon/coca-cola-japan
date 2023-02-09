<?php

namespace FortyFour\AdminGws\Plugin\Adminhtml\AdminGws;

use Magento\AdminGws\Model\Controllers;

class AdminGwsControllersPlugin extends AbstractAdminGwsPlugin
{
    /**
     * @param Controllers $subject
     * @param \Closure $proceed
     */
    public function aroundValidateCatalogProductAttributeActions(
        Controllers $subject,
        \Closure $proceed
    ) {
        $user = $this->getAdminUser();
        if ($user->getData('product_attribute_access') != 1) {
            return $proceed();
        }

        return true;
    }
}
