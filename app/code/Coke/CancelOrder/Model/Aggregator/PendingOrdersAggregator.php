<?php

namespace Coke\CancelOrder\Model\Aggregator;

use Coke\CancelOrder\Helper\Config;
use Coke\Whitelist\Api\WhitelistOrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Coke\CancelOrder\Logger\Logger;

class PendingOrdersAggregator
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var \Magento\Store\Api\Data\StoreInterface[]
     */
    private $stores;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var array
     */
    private $storeSearchCriteria = [];
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param Logger $logger
     * @param Config $config
     * @param StoreRepositoryInterface $storeRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Logger $logger,
        Config $config,
        StoreRepositoryInterface $storeRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ResourceConnection $resourceConnection,
        WhitelistOrderRepositoryInterface $whitelistOrderRepository
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->storeRepository = $storeRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->whitelistOrderRepository = $whitelistOrderRepository;
    }

    /**
     * @return array
     */
    public function getEnabledStoreIds(): array
    {
        $enabledStores = [];
        $stores = $this->getStores();
        foreach ($stores as $store) {
            if ($this->config->isEnabled($store->getId())) {
                $enabledStores[] = $store->getId();
            }
        }

        return $enabledStores;
    }

    /**
     * @param int $storeId
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getOrders(int $storeId): array
    {
        return $this->orderRepository->getList($this->getSearchCriteria($storeId))->getItems();
    }

    /**
     * @param $storeId
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function getSearchCriteria($storeId): \Magento\Framework\Api\SearchCriteria
    {
        if (!isset($this->storeSearchCriteria[$storeId])) {
            $this->storeSearchCriteria[$storeId] = $this->searchCriteriaBuilder->addFilter(
                'entity_id', $this->getOrderIds($storeId), 'in'
            )->create();
        }

        return $this->storeSearchCriteria[$storeId];
    }

    /**
     * @param $storeId
     * @return array
     */
    private function getOrderIds($storeId): array
    {
        $whitelistOrderId = $this->whitelistOrderRepository->getWhitelistOrderId();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('sales_order'),
            ['entity_id']
        )->where(
            'entity_id not in (?)', $whitelistOrderId
        )->where(
            'status = ?', $this->config->getOrderStatus($storeId)
        )->where(
            'store_id = ?', $storeId
        )->where(
                sprintf('created_at <= DATE_SUB(NOW(), INTERVAL %s MINUTE)', $this->config->getAgeLimit($storeId))
        );

        $this->logger->info(__('[PendingOrdersAggregator] query: %1', $select->__toString()));

        return $connection->fetchCol($select);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    private function getStores(): array
    {
        if (!$this->stores) {
            $this->stores = $this->storeRepository->getList();
        }

        return $this->stores;
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
