<?php

namespace CokeJapan\Hccb\Api;

use CokeJapan\Hccb\Api\Response\ShipmentResponseInterface;
use CokeJapan\Hccb\Api\Response\ResponseInterface;

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
     * @return string
     */
    public function getOrders(): string;
}
