<?php
namespace Coke\FaqCustom\Setup;

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
        $resultCategory = $setup->getConnection()->dropIndex('faq_category',$setup->getIdxName('faq_category', ['url_key']));
        $resultItem = $setup->getConnection()->dropIndex('faq_item',$setup->getIdxName('faq_item', ['url_key']));

        $setup->endSetup();

    }
}
