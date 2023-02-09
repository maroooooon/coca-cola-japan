<?php

namespace Coke\Whitelist\Api;

interface WhitelistManagementInterface
{
    /**
     * Changes whitelist entry to approved state. Finds all orders with this whitelist, if the current
     * entry being approved is the last pending whitelist entry for an order, the order is put into processing
     * state.
     *
     * @param $id
     * @return bool
     */
    public function approve($id);

    /**
     * Find all orders that contain the whitelist id, if no other whitelist items are pending,
     * change order status to processing.
     *
     * @param $whitelistId
     * @return bool
     */
    public function updateOrdersForWhitelistForApproved($whitelistId);

    /**
     * Find all orders that contain the whitelist id and change order status to denied.
     *
     * @param $whitelistId
     * @return bool
     */
    public function updateOrdersForWhitelistForDenied($whitelistId);

    /**
     * Changes a whitelist entry to denied state, and puts all orders containing this whitelist into denied state.
     *
     * @param $id
     * @return bool
     */
    public function deny($id);
}
