<?php

namespace FortyFour\Shipping\Api\ExpressStandard;

interface GuestDeliveryCommentManagementInterface
{
    /**
     * @param string $cartId
     * @param string $deliveryComment
     * @return bool
     */
    public function saveDeliveryComment($cartId, $deliveryComment);
}
