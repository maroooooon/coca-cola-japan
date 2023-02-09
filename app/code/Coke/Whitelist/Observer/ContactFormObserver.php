<?php

namespace Coke\Whitelist\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config as PageConfig;

class ContactFormObserver implements ObserverInterface
{
    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * ContactFormObserver constructor.
     * @param PageConfig $pageConfig
     */
    public function __construct(
        PageConfig $pageConfig
    )
    {
        $this->pageConfig = $pageConfig;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getRequest()->getParam('request', null);
//        $layout = $observer->getEvent()->getLayout();
//        $this->pageConfig->getPageLayout() ?: $this->getLayout()->getUpdate()->getPageLayout();
//        getUpdate()->addHandle('catalog_product_view_package_builder_handle');
        switch ($request) {
            case 'name' :
                $this->pageConfig->addBodyClass('request-name');
                $this->pageConfig->getTitle()->set(__('Request name form'));
                break;
            case 'pledge' :
                $this->pageConfig->addBodyClass('request-pledge');
                $this->pageConfig->getTitle()->set(__('Request pledge form'));
                break;
        }
    }
}
