<?php

namespace Coke\PersonalizedBottle\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class PersonalizedBottleLabelAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
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

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, "Marche");

        // Attribute Set Groups
        $general = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "General");
        $content = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "Content");
        $images = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "Images");

        //personalized_bottle_label
        if (!$eavSetup->getAttribute('catalog_product', 'personalized_bottle_label')) {
            $eavSetup->addAttribute('catalog_product', 'personalized_bottle_label', [
                'type' => 'int',
                'label' => 'Personalized Bottle Label',
                'input' => 'select',
                'required' => 0,
                'user_defined' => 1,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class
            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $general,
                'personalized_bottle_label',
                null
            );
        }

        if ($personalizedBottleLabel = $eavSetup->getAttribute('catalog_product', 'personalized_bottle_label')) {
            $additionalData = [
                'swatch_input_type' => 'visual',
                'update_product_preview_image' => '1',
                'use_product_image_for_swatch' => '0'
            ];
            $eavSetup->updateAttribute($entityTypeId, $personalizedBottleLabel['attribute_id'], 'additional_data', json_encode($additionalData));
        }

        //personalized_bottle_pack_count
        if (!$eavSetup->getAttribute('catalog_product', 'personalized_bottle_pack_count')) {
            $eavSetup->addAttribute('catalog_product', 'personalized_bottle_pack_count', [
                'type' => 'int',
                'label' => 'Personalized Bottle Pack Count',
                'input' => 'select',
                'required' => 0,
                'user_defined' => 1,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class
            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $general,
                'personalized_bottle_pack_count',
                null
            );
        }

        if ($personalizedBottleLabel = $eavSetup->getAttribute('catalog_product', 'personalized_bottle_pack_count')) {
            $additionalData = [
                'swatch_input_type' => 'text',
                'update_product_preview_image' => '0',
                'use_product_image_for_swatch' => '0'
            ];
            $eavSetup->updateAttribute($entityTypeId, $personalizedBottleLabel['attribute_id'], 'additional_data', json_encode($additionalData));
        }

        //label_pos_offset
        if (!$eavSetup->getAttribute('catalog_product', 'label_pos_offset')) {
            $eavSetup->addAttribute('catalog_product', 'label_pos_offset', [
                'type' => 'int',
                'label' => 'Personalized Bottle Label Y Position Offset',
                'input' => 'text',
                'required' => 0,
                'user_defined' => 1
            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $images,
                'label_pos_offset',
                null
            );
        }

        //label_pos_offset
        if (!$eavSetup->getAttribute('catalog_product', 'personalized_bottle_header_ste')) {
            $eavSetup->addAttribute('catalog_product', 'personalized_bottle_header_ste', [
                'type' => 'text',
                'label' => 'Personalized Bottle Header Steps',
                'input' => 'textarea',
                'required' => 0,
                'user_defined' => 1,
                'is_wysiwyg_enabled' => 1,
                'is_pagebuilder_enabled' => 1
            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $content,
                'personalized_bottle_header_ste',
                null
            );
        }

        //recommended_products
        if (!$eavSetup->getAttribute('catalog_product', 'recommended_products')) {
            $eavSetup->addAttribute('catalog_product', 'recommended_products', [
                'type' => 'int',
                'label' => 'Recommended Products',
                'input' => 'boolean',
                'required' => 0,
                'user_defined' => 1,
                'default' => 0,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1

            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $general,
                'recommended_products',
                null
            );
        }

        //sales_unit
        if (!$eavSetup->getAttribute('catalog_product', 'sales_unit')) {
            $eavSetup->addAttribute('catalog_product', 'sales_unit', [
                'type' => 'int',
                'label' => 'Sales Unit',
                'input' => 'select',
                'required' => 0,
                'user_defined' => 1,
                'default' => 0,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1

            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $general,
                'sales_unit',
                null
            );
        }

        //js_code
        if (!$eavSetup->getAttribute('catalog_product', 'js_code')) {
            $eavSetup->addAttribute('catalog_product', 'js_code', [
                'type' => 'varchar',
                'label' => 'JS Code',
                'input' => 'text',
                'required' => 0,
                'user_defined' => 1,
                'default' => 0,
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1

            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $general,
                'js_code',
                null
            );
        }
    }
}
