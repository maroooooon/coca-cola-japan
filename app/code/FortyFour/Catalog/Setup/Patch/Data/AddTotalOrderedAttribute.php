<?php

namespace FortyFour\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddTotalOrderedAttribute implements DataPatchInterface
{
    const TOTAL_QTY_ORDERED_ATTRIBUTE = 'total_qty_ordered';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;
    /**
     * @var State
     */
    private $state;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param State $state
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->state = $state;
    }

    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        if (!$eavSetup->getAttribute(Product::ENTITY, self::TOTAL_QTY_ORDERED_ATTRIBUTE)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                self::TOTAL_QTY_ORDERED_ATTRIBUTE,
                [
                    'type' => 'decimal',
                    'label' => 'Total Quantity Ordered',
                    'input' => 'text',
                    'required' => 0,
                    'user_defined' => 0,
                    'default' => 0,
                    'source' => '',
                    'is_used_in_grid' => 0,
                    'is_visible_in_grid' => 0,
                    'is_filterable_in_grid' => 0,
                    'apply_to' => '',
//                    'used_in_product_listing' => 1,
//                    'used_for_sort_by' => 1,
                    'is_used_for_promo_rules' => 1
                ]
            );
        }
    }
}
