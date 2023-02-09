<?php

namespace Coke\Whitelist\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;

class AttachDeniedAndUnderReviewStatusToState implements DataPatchInterface
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
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->attachStatusToState('denied', 'canceled');
        $this->attachStatusToState('under_review', 'holded');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @param string $status
     * @param string $state
     * @return void
     */
    private function attachStatusToState(string $status, string $state)
    {
        $this->moduleDataSetup->getConnection()->insert(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            [
                'status' => $status,
                'state' => $state,
                'is_default' => 0,
                'visible_on_front' => 1
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [AddDeniedOrderStatus::class, AddUnderReviewOrderStatus::class];
    }


    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
