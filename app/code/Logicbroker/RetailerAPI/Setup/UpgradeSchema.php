<?php
namespace Logicbroker\RetailerAPI\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $orderTable = $installer->getTable('sales_order');
        if ($installer->getConnection()->tableColumnExists($orderTable, 'logicbroker_key') == false) {
            $installer->getConnection()->addColumn(
                $orderTable,
                'logicbroker_key',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Logicbroker ID',
                ]
            );
        }

        if ($installer->getConnection()->tableColumnExists($orderTable, 'custom_invoice_number') == false) {
            $installer->getConnection()->addColumn(
                $orderTable,
                'custom_invoice_number',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Custom Invoice Number',
                ]
            );
        }

        $orderGridTable = $installer->getTable('sales_order_grid');
        if ($installer->getConnection()->tableColumnExists($orderGridTable, 'logicbroker_key') == false) {
            $installer->getConnection()->addColumn(
                $orderGridTable,
                'logicbroker_key',
                [
                  'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Logicbroker ID',
                ]
            );
        }

        $this->createInventoryHistoryTable($installer);

        $installer->endSetup();
    }

    protected function createInventoryHistoryTable($installer)
    {
        $tableName = $installer->getTable('logicbroker_inventory_history');
        if ($installer->getConnection()->isTableExists($tableName) == false) {
            $table = $installer->getConnection()->newTable($tableName)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'partnerid',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Partner ID'
                    )
                    ->addColumn(
                        'date',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false],
                        'Date'
                    )
                    ->addColumn(
                        'total_items',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Total Items'
                    )
                    ->addColumn(
                        'message',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => true],
                        'Message'
                    );
            $installer->getConnection()->createTable($table);
        }
    }
}
