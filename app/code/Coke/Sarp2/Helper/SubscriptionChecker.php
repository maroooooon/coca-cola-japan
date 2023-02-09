<?php

namespace Coke\Sarp2\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class SubscriptionChecker
{
    /**
     * @var AdapterInterface
     */
    private $connection;
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param Session $checkoutSession
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Session $checkoutSession,
        ResourceConnection $resourceConnection
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return int|null
     */
    public function getQuoteId(): ?int
    {
        return $this->checkoutSession->getQuoteId();
    }

    /**
     * @param int $quoteId
     * @return bool
     */
    public function isSubscription(int $quoteId): bool
    {
        $connection = $this->getConnection();
        $sql = $connection->select()->from(
            $connection->getTableName('quote'),
            ['aw_sarp_regular_subtotal']
        )->where('entity_id = ?', $quoteId);

        return $connection->fetchOne($sql) > 0;
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
