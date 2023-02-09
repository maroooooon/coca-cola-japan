<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class TopoChicoAttributes implements DataPatchInterface
{
    private $eavSetupFactory;
    private $attributeSetFactory;
    private $categorySetupFactory;
    private $moduleDataSetup;
    private $attributeGroupRepository;
    private $attributeGroupFactory;

    const TOPOCHICO_ATTRIBUTES = [
        'brand',
        'flavor',
        'is_alcoholic',
        'nutritional',
        'ingredients'
    ];

    public function __construct(
        EavSetupFactory          $eavSetupFactory,
        AttributeSetFactory      $attributeSetFactory,
        CategorySetupFactory     $categorySetupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeGroupRepositoryInterface $attributeGroupRepository,
        AttributeGroupInterfaceFactory $attributeGroupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    public function apply()
    {
//      Setup the data patch
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributeGroup = $this->attributeGroupFactory->create();

//      Get Attribute Set
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, "Merchandise");
        $attributeSet->load($attributeSetId);

//      Create Attribute Group
        $attributeGroup->setAttributeSetId($attributeSetId);
        $attributeGroup->setAttributeGroupName('Drinks');

        $this->attributeGroupRepository->save($attributeGroup);

//      Retrieve Group ID
        $topochicoGroupID = $attributeGroup->getAttributeGroupId();

//      Adds existing attributes to the new attribute group called "Drinks"
        foreach (self::TOPOCHICO_ATTRIBUTES as $attribute) {
            $eavSetup->addAttributeToSet($entityTypeId, $attributeSetId, $topochicoGroupID, $attribute);
        }

//      Validate and save the attribute set
        $attributeSet->validate();
        $attributeSet->save();
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [
            \CokeEurope\StoreModifications\Setup\Patch\Data\RenamingAttributeSets::class
        ];
    }
}
