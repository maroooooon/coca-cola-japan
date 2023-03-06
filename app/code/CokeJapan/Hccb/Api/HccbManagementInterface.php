<?php

namespace CokeJapan\Hccb\Api;

use CokeJapan\Hccb\Api\Response\ShipmentResponseInterface;

interface HccbManagementInterface
{
    /**
     * Create shipment order
     *
     * @return ShipmentResponseInterface
     */
    public function createShipments();

    /**
     * Get order processing
     *
     * @return array
     */
    public function getOrders();
}
