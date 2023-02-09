<?php
/**
 * BeforeCustomerAddressSaveApartmentFlat
 *
 * @copyright Copyright © 2023 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Customer\Plugin;

use Magento\Customer\Controller\Address\FormPost;

class BeforeCustomerAddressSaveApartmentFlat
{
    /**
     * It takes the value of the `street_flat` field and prepends it to the value of the `street` field
     *
     * @param FormPost $subject The FormPost object
     *
     * @return array An array of parameters.
     */
    public function beforeExecute(FormPost $subject): array
    {
        if ($subject->getRequest()->getParam('street_flat') !== null) {
            $params = $subject->getRequest()->getParams();

            $formStreetLine1 = $params['street'][0];
            $formStreetFlat = $subject->getRequest()->getParam('street_flat');

            $params['street'][0] = sprintf('%s %s', $formStreetFlat, $formStreetLine1);
            $subject->getRequest()->setParams($params);
        };
        return [];
    }
}
