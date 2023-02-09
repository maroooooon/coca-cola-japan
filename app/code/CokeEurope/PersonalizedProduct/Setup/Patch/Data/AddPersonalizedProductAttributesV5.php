<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\PersonalizedProduct\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddPersonalizedProductAttributesV5 implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);


        $eavSetup->addAttribute(
            Product::ENTITY,
            'prefilled_message',
            [
                'type' => 'int',
                'label' => 'Prefilled Message',
                'input' => 'select',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'option' => ['values' => ["Happy Easter","Happy Birthday","Merry Christmas"]],
                'sort_order' => '1',
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_x',
            [
                'type' => 'varchar',
                'label' => 'Label Position (X)',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'sort_order' => '2'
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_y',
            [
                'type' => 'varchar',
                'label' => 'Label Position (Y)',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'sort_order' => '3'
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_width',
            [
                'type' => 'varchar',
                'label' => 'Label Width',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'sort_order' => '4'
            ]
        );
		$eavSetup->addAttribute(
			Product::ENTITY,
			'pp_label_color',
			[
				'type' => 'varchar',
				'label' => 'Label Color',
				'input' => 'text',
				'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
				'visible' => true,
				'required' => false,
				'user_defined' => true,
				'default' => '#ffffff',
				'unique' => false,
				'searchable' => false,
				'filterable' => false,
				'comparable' => false,
				'visible_on_front' => true,
				'is_used_in_grid' => true,
				'is_visible_in_grid' => false,
				'is_filterable_in_grid' => false,
				'used_in_product_listing' => false,
				'sort_order' => '5'
			]
		);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_font_size',
            [
                'type' => 'varchar',
                'label' => 'Label Font Size',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'sort_order' => '6'
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_font_family',
            [
                'type' => 'int',
                'label' => 'Label Font Family',
                'input' => 'select',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'option' => ['values' => ["You","TCCC-UnityHeadline","Gotham"]],
                'sort_order' => '7',
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_character_limit',
            [
                'type' => 'varchar',
                'label' => 'Label Character Limit',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'sort_order' => '8'
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'pp_label_regex',
            [
                'type' => 'varchar',
                'label' => 'Label Allowed Characters (REGEX)',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'unique' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => false,
                'sort_order' => '9'
            ]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, 'prefilled_message');
        $eavSetup->removeAttribute(Product::ENTITY, 'pp_label_x');
        $eavSetup->removeAttribute(Product::ENTITY, 'pp_label_y');
        $eavSetup->removeAttribute(Product::ENTITY, 'pp_label_width');
		$eavSetup->removeAttribute(Product::ENTITY, 'pp_label_color');
		$eavSetup->removeAttribute(Product::ENTITY, 'pp_label_font_size');
        $eavSetup->removeAttribute(Product::ENTITY, 'pp_label_font_family');
        $eavSetup->removeAttribute(Product::ENTITY, 'pp_label_character_limit');
        $eavSetup->removeAttribute(Product::ENTITY, 'pp_label_regex');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
	{
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
	{
        return [];
    }
}
