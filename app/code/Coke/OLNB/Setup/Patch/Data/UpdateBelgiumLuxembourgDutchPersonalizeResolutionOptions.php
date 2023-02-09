<?php

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateBelgiumLuxembourgDutchPersonalizeResolutionOptions implements DataPatchInterface
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
        $this->updateTitleAndSortOrder('From', 'Van', 3);
        $this->updateTitleAndSortOrder('To', 'Voor', 2);
        $this->updateTitleAndSortOrder('Pour', 'Voor', 2);

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
     * @param int $storeId
     * @return array
     */
    private function getProductIds(int $storeId)
    {
        $finnishProductsTable = sprintf('catalog_category_product_index_store%s', $storeId);
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $connection->getTableName($finnishProductsTable),
            'product_id'
        )->distinct(true);

        return $connection->fetchAll($select);
    }

    /**
     * @param int $storeId
     * @param string $title
     * @return array
     */
    private function getCustomProductOptionData(int $storeId, string $title): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['cpo' => $connection->getTableName('catalog_product_option')],
            [
                'option_id' => 'cpo.option_id',
                'option_title_id' => 'cpot.option_title_id',
            ]
        )->joinInner(
            ['cpot' => $connection->getTableName('catalog_product_option_title')],
            'cpo.option_id = cpot.option_id',
            []
        )->where(
            'cpo.product_id IN (?)',
            $this->getProductIds($storeId)
        )->where(
            "cpo.step_label = 'Personalize your Resolution'"
        )->where(
            "cpot.title = ?", $title
        )->distinct(true);

        return $connection->fetchAll($select);
    }

    /**
     * @param int $storeId
     * @param string $title
     * @return array
     */
    private function getOptionIds(int $storeId, string $title): array
    {
        $ids = [];
        $items = $this->getCustomProductOptionData($storeId, $title);
        foreach ($items as $item) {
            $ids[] = $item['option_id'];
        }

        return $ids;
    }

    /**
     * @param int $storeId
     * @param string $title
     * @return array
     */
    private function getOptionTitleIds(int $storeId, string $title): array
    {
        $ids = [];
        $items = $this->getCustomProductOptionData($storeId, $title);
        foreach ($items as $item) {
            $ids[] = $item['option_title_id'];
        }

        return $ids;
    }

    /**
     * @param string $title
     * @param string $newTitle
     * @param int $sortOrder
     */
    private function updateTitleAndSortOrder(string $title, string $newTitle, int $sortOrder)
    {
        $storeId = $this->getStoreIdByCode('belgium_luxembourg_dutch');
        $titleIds = $this->getOptionTitleIds($storeId, $title);
        $optionIds = $this->getOptionIds($storeId, $title);
        $connection = $this->getConnection();

        $connection->update(
            $connection->getTableName('catalog_product_option_title'),
            ['title' => $newTitle],
            ['option_title_id IN (?)' => $titleIds]
        );

        $connection->update(
            $connection->getTableName('catalog_product_option'),
            ['sort_order' => $sortOrder],
            ['option_id IN (?)' => $optionIds]
        );
    }
}

