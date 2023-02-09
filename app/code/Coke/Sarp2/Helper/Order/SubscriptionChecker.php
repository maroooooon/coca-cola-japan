<?php

namespace Coke\Sarp2\Helper\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

class SubscriptionChecker
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param int $orderId
     * @return bool
     */
    public function isSubscription(int $orderId): bool
    {
        $connection = $this->getConnection();
        $query = $connection->select()->from(
            $connection->getTableName('aw_sarp2_profile_order'),
            'profile_id'
        )->where('order_id = ?', $orderId);

        return (bool)$connection->fetchOne($query);
    }

    /**
     * @param int $orderId
     * @return bool
     */
    public function hasQuoteId(int $orderId): bool
    {
        $connection = $this->getConnection();
        $query = $connection->select()->from(
            $connection->getTableName('sales_order'),
            'quote_id'
        )->where('entity_id = ?', $orderId);

        return (bool)$connection->fetchOne($query);
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
