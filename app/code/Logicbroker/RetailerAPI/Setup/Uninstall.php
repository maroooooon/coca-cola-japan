<?php
namespace Logicbroker\RetailerAPI\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if ($installer->getConnection()->tableColumnExists('sales_order', 'logicbroker_key')) {
            $installer->getConnection()->dropColumn($installer->getTable('sales_order'), 'logicbroker_key');
        }

        if ($installer->getConnection()->tableColumnExists('sales_order', 'custom_invoice_number')) {
            $installer->getConnection()->dropColumn($installer->getTable('sales_order'), 'custom_invoice_number');
        }

        if ($installer->getConnection()->tableColumnExists('sales_order_grid', 'logicbroker_key')) {
            $installer->getConnection()->dropColumn($installer->getTable('sales_order_grid'), 'logicbroker_key');
        }

        $this->dropTable($installer, 'logicbroker_inventory_history');
        $installer->endSetup();
    }

    protected function dropTable($installer, $table)
    {
        $tableName = $installer->getTable($table);
        if ($installer->getConnection()->isTableExists($tableName) == true) {
            $installer->getConnection()->dropTable($tableName);
        }
    }
}
