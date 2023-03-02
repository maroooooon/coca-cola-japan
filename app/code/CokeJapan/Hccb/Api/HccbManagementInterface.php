<?php

namespace CokeJapan\Hccb\Api;

interface HccbManagementInterface
{
    /**
     * Create shipment order
     *
     * @return string
     */
    public function createShipments(): string;

    /**
     * Get order processing
     *
     * @return string
     */
    public function getOrders(): string;
}
