<?php

namespace Coke\Whitelist\Api\Data;

interface WhitelistInterface
{
    const ENTITY_ID = 'entity_id';
    const TYPE_ID = 'type_id';
    const VALUE = 'value';
    const STATUS = 'status';
    const STORE_ID = 'store_id';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     */
    public function setEntityId($entityId);

    /**
     * Get whitelist type ID
     *
     * @return int
     */
    public function getTypeId(): int;

    /**
     * Set whitelist type ID
     *
     * @param int $type
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     */
    public function setTypeId(int $typeId): WhitelistInterface;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @param string $value
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     */
    public function setValue(string $value): WhitelistInterface;

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @param $storeId
     * @return WhitelistInterface
     */
    public function setStoreId($storeId): WhitelistInterface;

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param $status
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     */
    public function setStatus($status): WhitelistInterface;
}
