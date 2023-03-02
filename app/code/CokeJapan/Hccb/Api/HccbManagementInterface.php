<?php

namespace CokeJapan\Hccb\Api;

interface HccbManagementInterface
{
    /**
     * @return string
     */
    public function createShipments(): string;

    /**
     * @return string
     */
    public function getOrders(): string;
}
