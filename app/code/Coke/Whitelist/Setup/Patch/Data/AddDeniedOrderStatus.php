<?php

namespace Coke\Whitelist\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddDeniedOrderStatus implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(\Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->moduleDataSetup->getConnection()->insert(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status' => 'denied', 'label' => __('Order Denied')]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }


    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
