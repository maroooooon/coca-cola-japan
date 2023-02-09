<?php

namespace Coke\Whitelist\Model\Aggregator;

use Coke\Whitelist\Model\ModuleConfig;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Coke\CancelOrder\Logger\Logger;

class DeniedOrdersAggregator
{
    /**
     * @var Logger
     */
    private $logger;
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
     * @var ModuleConfig
     */
    private $config;

    /**
     * @param Logger $logger
     * @param StoreRepositoryInterface $storeRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ResourceConnection $resourceConnection
     * @param ModuleConfig $config
     */
    public function __construct(
        Logger $logger,
        StoreRepositoryInterface $storeRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ResourceConnection $resourceConnection,
        ModuleConfig $config
    ) {
        $this->logger = $logger;
        $this->storeRepository = $storeRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getEnabledStoreIds(): array
    {
        $enabledStores = [];
        $stores = $this->getStores();
        foreach ($stores as $store) {
            if ($this->config->isCancelDeniedOrderEnabled($store->getId())) {
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
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('sales_order'),
            ['entity_id']
        )->where(
            'status = ?', $this->config->getDeniedWhitelistItemOrderStatus($storeId)
        )->where(
            'store_id = ?', $storeId
        );

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
