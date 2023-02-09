<?php

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateFinnishCompleteYourPledgeTranslations implements DataPatchInterface
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
        return [];
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
     */
    public function apply()
    {
        $this->updateCustomProductOptionStepLabels();
        $this->updateCustomProductOptionTitles();

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
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFinnishStoreId()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store'),
            'store_id'
        )->where("code = 'finland_finnish'");

        return $connection->fetchOne($select);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFinnishProductIds()
    {
        $finnishProductsTable = sprintf('catalog_category_product_index_store%s', $this->getFinnishStoreId());
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $connection->getTableName($finnishProductsTable),
            'product_id'
        )->distinct(true);

        return $connection->fetchAll($select);
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateCustomProductOptionStepLabels()
    {
        $connection = $this->getConnection();
        $bind = ['step_label' => 'Viimeistely'];
        $where = ['product_id IN (?)' => $this->getFinnishProductIds()];
        $connection->update(
            $connection->getTableName('catalog_product_option'),
            $bind,
            $where
        );
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomProductOptionIds(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('catalog_product_option'),
            'option_id'
        )->where(
            'product_id IN (?)',
            $this->getFinnishProductIds()
        )->distinct(true);

        return $connection->fetchAll($select);
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateCustomProductOptionTitles()
    {
        $connection = $this->getConnection();
        $bind = ['title' => 'Viimeistely'];
        $where = ['option_id IN (?)' => $this->getCustomProductOptionIds()];
        $connection->update(
            $connection->getTableName('catalog_product_option_title'),
            $bind,
            $where
        );
    }
}

