<?php

namespace Coke\FAQ\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;


/**
 * Class UpgradeSchema
 * 
 * @package Coke\FAQ\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $setup->startSetup();
        $tableName = 'faq_item';
        $columns = [
            'title' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'FAQ item title'
            ],
            'tags' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Tags'
            ]
        ];
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->changeColumn($tableName, $name, $name, $definition);
            }
        }
        $setup->endSetup();
    }
}