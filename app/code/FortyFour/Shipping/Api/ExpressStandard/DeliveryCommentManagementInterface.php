<?php

namespace FortyFour\Shipping\Api\ExpressStandard;

interface DeliveryCommentManagementInterface
{
    /**
     * @param int $cartId
     * @param string $deliveryComment
     * @return bool
     */
    public function saveDeliveryComment($cartId, $deliveryComment);
}
