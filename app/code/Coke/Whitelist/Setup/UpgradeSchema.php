<?php

namespace Coke\Whitelist\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $sql = /** @lang text */
                'ALTER TABLE coke_whitelist CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin';
            $installer->getConnection()->query($sql);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $sql = /** @lang text */
                'ALTER TABLE coke_whitelist CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
            $installer->getConnection()->query($sql);
        }

        $installer->endSetup();
    }
}
