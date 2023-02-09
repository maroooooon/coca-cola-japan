<?php

namespace Coke\Sarp2\Plugin\Adminhtml;

class ProfileGridCollectionPlugin
{
    /**
     * @param \Aheadworks\Sarp2\Model\ResourceModel\Profile\Grid\Collection $subject
     */
   public function beforeGetSelect(
       \Aheadworks\Sarp2\Model\ResourceModel\Profile\Grid\Collection $subject
   ) {
       /**
        * Prevents profile_id from being ambiguous
        * @see \Coke\Sarp2\Observer\SubscriptionGridCollectionLoadBefore::execute()
        */
       $subject->addFilterToMap('profile_id', 'main_table.profile_id');
   }
}
