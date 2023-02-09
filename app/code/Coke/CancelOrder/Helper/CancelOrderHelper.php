<?php

namespace Coke\CancelOrder\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CancelOrderHelper
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param ResourceConnection $resourceConnection
     * @param UrlInterface $url
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        UrlInterface $url
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->url = $url;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getCancelOrderUrl(OrderInterface $order): string
    {
        return $this->url->getUrl('sales/order/cancelOrder', ['order_id' => $order->getEntityId()]);
    }

    /**
     * @param $status
     * @return string
     */
    public function getStateFromStatus($status)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('sales_order_status_state'),
            ['state']
        )->where('status = ?', $status);

        return $connection->fetchOne($select);
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function hasSubscription(OrderInterface $order): bool
    {
        if ($this->getProfileIdFromOrder($order)) {
            return true;
        }

        return false;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function getProfileIdFromOrder(OrderInterface $order)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('aw_sarp2_profile_order'),
            ['profile_id']
        )->where('order_id = ?', $order->getEntityId());

        return $connection->fetchOne($select);
    }


    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }
}
