<?php

namespace Coke\Whitelist\Api\Data;

interface WhitelistOrderInterface
{
    const ENTITY_ID = 'entity_id';
    const WHITELIST_ID = 'whitelist_id';
    const ORDER_ID = 'order_id';

    public function getEntityId();

    /**
     * @param $entityId
     * @return WhitelistOrderInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getWhitlistId(): string;

    /**
     * @param string $whitelistId
     * @return WhitelistOrderInterface
     */
    public function setWhitelistId(string $whitelistId): WhitelistOrderInterface;

    /**
     * @return string
     */
    public function getOrderId(): string;

    /**
     * @param string $orderId
     * @return WhitelistOrderInterface
     */
    public function setOrderId(string $orderId): WhitelistOrderInterface;
}
