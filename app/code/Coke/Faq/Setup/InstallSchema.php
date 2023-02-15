<?php
namespace Coke\Faq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'faq_category'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('faq_category'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'FAQ Category Name'
            )    
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                array(),
                'Active Status'
            )
            ->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array(),
                'Sort Order'
            )
            ->addColumn(
                'url_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'URL Key'
            )    
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Modification Time'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store id'
            )->addIndex(
                $setup->getIdxName('faq_category', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName('faq_category', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )    
            ->addIndex(
                $setup->getIdxName('faq_category', ['entity_id']),
                ['entity_id']
            )
            ->addIndex(
                $setup->getIdxName('faq_category', ['url_key']),
                ['url_key'],
                ['type' => 'UNIQUE']    
            )    
            ->setComment('Coke FAQ categories table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'faq_item'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('faq_item'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'FAQ item title'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )    
            ->addColumn(
                'tags',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Tags'
            )
            ->addColumn(
                'faq_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'FAQ Category Id'
            )  
            ->addColumn(
                'url_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'URL Key'
            )    
            ->addColumn(
                'most_frequently',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                array(),
                'Most Frequently'
            )    
            ->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array(),
                'Sort Order'
            )    
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                array(),
                'Active Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Modification Time'
            )    
            ->addIndex(
                $setup->getIdxName('faq_item', ['entity_id']),
                ['entity_id']
            )
            ->addIndex(
                $setup->getIdxName('faq_item', ['faq_category_id']),
                ['faq_category_id']
            )
            ->addIndex(
                $setup->getIdxName('faq_item', ['url_key']),
                ['url_key'],
                ['type' => 'UNIQUE']    
            )     
            ->addForeignKey(
                $setup->getFkName('faq_item', 'faq_category_id', 'faq_category', 'entity_id'),
                'faq_category_id',
                $setup->getTable('faq_category'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Coke FAQ items table');
        $setup->getConnection()->createTable($table);        
        
        
        $setup->endSetup();

    }
}
