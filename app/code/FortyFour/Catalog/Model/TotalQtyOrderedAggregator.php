<?php

namespace FortyFour\Catalog\Model;

use FortyFour\Catalog\Setup\Patch\Data\AddTotalOrderedAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class TotalQtyOrderedAggregator
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
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        ProductRepositoryInterface $productRepository,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     */
    public function insertTotalQtyOrdered()
    {
        $eavSetup = $this->eavSetupFactory->create();
        $attributeId = $eavSetup->getAttributeId(
            Product::ENTITY,
            AddTotalOrderedAttribute::TOTAL_QTY_ORDERED_ATTRIBUTE
        );
        $rows = $this->getTotalOrderedData();
        foreach ($rows as $row) {
            try {
                $this->saveProductTotalQtyOrdered($row['sku'], $attributeId, $row['qty_ordered']);
            } catch (NoSuchEntityException $e) {
                $this->logger->info(
                    __('[TotalQtyOrderedAggregator] SKU: %1. Error: %2', $row['sku'], $e->getMessage())
                );
                continue;
            }
        }
    }

    /**
     * @return array
     */
    private function getTotalOrderedData(): array
    {
        $connection = $this->getConnection();
        $query = $connection->select()->from(
            $connection->getTableName('sales_order_item'),
            [
                'sku' => 'sku',
                'qty_ordered' => 'SUM(qty_ordered)',
            ]
        )->group('product_id');

        return $connection->fetchAll($query);
    }

    /**
     * @param string $sku
     * @param int $attributeId
     * @param string $totalQtyOrdered
     * @throws NoSuchEntityException
     */
    private function saveProductTotalQtyOrdered(string $sku, int $attributeId, string $totalQtyOrdered)
    {
        $product = $this->productRepository->get($sku);
        $this->getConnection()->insertOnDuplicate(
            'catalog_product_entity_decimal',
            [
                'attribute_id' => $attributeId,
                'store_id' => 0,
                'row_id' => $product->getData('row_id'),
                'value' => $totalQtyOrdered
            ],
            ['value']
        );
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
