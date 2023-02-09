<?php

namespace CokeEurope\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use CokeEurope\Customer\Helper\Config;
use CokeEurope\PersonalizedProduct\Helper\Config as PersonalizedProductConfig;
use CokeEurope\Catalog\Api\Data\ModerationStatusInterface;

class AddCustomVariableToEmailTemplate implements ObserverInterface
{


    private Config $configHelper;
    private PersonalizedProductConfig $personalizedProductConfigHelper;

    public function __construct(
        Config $configHelper,
        PersonalizedProductConfig $personalizedProductConfigHelper
    ) {
        $this->configHelper = $configHelper;
        $this->personalizedProductConfigHelper = $personalizedProductConfigHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {

        $transport = $observer->getEvent()->getData('transportObject');
        $order = $transport->getOrder();
        $orderData = $transport->getOrderData();
        // sets to false even if moderation is disabled to avoid breaking email variable.
        // variable used to show correct messaging
        $orderData['pending_approval'] = false;
        if ($contactFormUrl = $this->configHelper->getContactFormUrl($order->getStoreId())) {
            $orderData['contact_form_url'] = $contactFormUrl;
        }
        if ($this->personalizedProductConfigHelper->getModerationEnabled()){
            foreach ($order->getItems() as $item){
                $moderationStatus = (int) $item->getData('moderation_status');
                if(!empty($moderationStatus) && $moderationStatus === ModerationStatusInterface::MODERATION_STATUS_PENDING){
                    //sets to true if any product has moderation_status
                    $orderData['pending_approval'] = true;
                }
            }
        }

        $transport->setData('order_data', $orderData);
    }
}
