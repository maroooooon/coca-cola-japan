<?php

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class UpdateBelgiumLuxembourgDutchPersonalizeResolutionsSortOrder implements DataPatchInterface
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
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * UpdateFinnishCompleteYourPledgeTranslations constructor.
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
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            UpdateBelgiumLuxembourgDutchPersonalizeResolutionOptions::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|DataPatchInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function apply()
    {
        $this->updateDutchSortOrder(2, 'Voor');
        $this->updateDutchSortOrder(3, 'Van');

        return $this;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param string $code
     * @return string
     */
    private function getStoreIdByCode(string $code)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store'),
            'store_id'
        )->where('code = ?', $code);

        return $connection->fetchOne($select);
    }

    /**
     * @param int $sortOrder
     * @param string $title
     */
    private function updateDutchSortOrder(int $sortOrder, string $title)
    {
        $storeId = $this->getStoreIdByCode('belgium_luxembourg_dutch');
        $connection = $this->getConnection();
        $query = sprintf(
            /** @lang text */
            "UPDATE catalog_product_option o
            INNER JOIN catalog_product_option_title t ON o.option_id = t.option_id
            SET o.sort_order = %s
            WHERE o.product_id IN (
                SELECT product_id
                FROM catalog_category_product_index_store%s
            ) AND t.title = '%s'",
            $sortOrder,
            $storeId,
            $title
        );

        $connection->query($query);
    }
}

