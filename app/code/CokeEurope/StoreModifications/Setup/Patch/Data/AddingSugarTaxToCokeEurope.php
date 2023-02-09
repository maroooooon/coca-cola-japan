<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Api\AttributeSetRepositoryInterface;

class AddingSugarTaxToCokeEurope implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory$eavSetupFactory;
    private AttributeSetFactory $attributeSetFactory;
    private CategorySetupFactory $categorySetupFactory;
    private AttributeSetRepositoryInterface $attributeSetRepository;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        AttributeSetRepositoryInterface $attributeSetRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeSetRepository = $attributeSetRepository;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);

        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, "Personalization");
        $generalGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "General");

        $this->addSugarTaxAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
    }

    /**
     * @param EavSetup $eavSetup
     * @param int $entityTypeId
     * @param int $attributeSetId
     * @param int $generalGroupId
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addSugarTaxAttribute(
        EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            Product::ENTITY,
            'sugar_tax',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Sugar Tax',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'search_weight' => '4',
                'default' => 0.00,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
            ]
        );
        $this->addAttributeToSetAndGroup(
            $eavSetup,
            $entityTypeId,
            $attributeSetId,
            $generalGroupId,
            'sugar_tax'
        );
    }

    /**
     * @param $eavSetup
     * @param $entityTypeId
     * @param $attributeSetId
     * @param $generalGroupId
     *
     * @return void
     */
    private function addAttributeToSetAndGroup(
        $eavSetup,
        $entityTypeId,
        $attributeSetId,
        $generalGroupId,
        $attributeCode
    ): void {
        $attribute = $eavSetup->getAttribute($entityTypeId, $attributeCode);
        $eavSetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $generalGroupId,
            $attribute['attribute_id']
        );
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
