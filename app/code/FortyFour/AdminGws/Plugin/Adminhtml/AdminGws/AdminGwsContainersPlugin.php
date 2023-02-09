<?php

namespace FortyFour\AdminGws\Plugin\Adminhtml\AdminGws;

use Magento\Backend\Block\Widget\ContainerInterface;

class AdminGwsContainersPlugin extends AbstractAdminGwsPlugin
{
    /**
     * @param \Magento\AdminGws\Model\Containers $subject
     * @param \Closure $proceed
     * @param ContainerInterface $container
     * @return mixed|void
     */
    public function aroundRemoveCatalogProductAttributeButtons(
        \Magento\AdminGws\Model\Containers $subject,
        \Closure $proceed,
        ContainerInterface $container
    ) {
        $user = $this->getAdminUser();
        if ($user->getData('product_attribute_access') != 1) {
            return $proceed($container);
        }

        $container->removeButton('delete');
    }
}
