<?php

namespace Coke\Sarp2\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SubscriptionGridCollectionLoadBefore implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Aheadworks\Sarp2\Model\ResourceModel\Profile\Grid\Collection $collection */
        $collection = $observer->getSubscriptionGridCollection();
        $collection->getSelect()->joinLeft(
            ['profile_item' => 'aw_sarp2_profile_item'],
            'main_table.profile_id = profile_item.profile_id',
            ['sku' => new \Zend_Db_Expr("GROUP_CONCAT(profile_item.sku)")]
        )->group('main_table.profile_id');

        return $this;
    }
}
