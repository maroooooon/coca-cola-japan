<?php

namespace Coke\Sarp2\Plugin\Model\Profile;

use Aheadworks\Sarp2\Model\Profile\Finder;
use Magento\Framework\App\RequestInterface;

class FinderPlugin
{
    const ACTION_ORDER_VIEW = 'sales_order_view';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
       $this->request = $request;
    }

    /**
     * GetByOrderIdAndPlanId
     *
     * @param Finder $subject
     * @param $result
     * @param int $orderId
     * @param int $planId
     * @return mixed|null
     */
    public function afterGetByOrderIdAndPlanId(Finder $subject, $result, int $orderId, int $planId)
    {
       if ($this->request->getFullActionName() == self::ACTION_ORDER_VIEW) {
           $profiles = $subject->getByOrder($orderId);
           foreach ($profiles as $profile) {
               if ((int)$profile->getPlanId() === $planId) {
                   $result[$profile->getProfileId()] = $profile;
               }
           }
           if (!$result && isset($profiles[0])) {
               $result[$profiles[0]->getProfileId()] = $profiles[0];
           }
       }

        return $result;

    }

}