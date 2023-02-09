<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RenamingAttributeSets implements DataPatchInterface
{
    private $eavSetupFactory;
    private $attributeSetFactory;
    private $categorySetupFactory;
    private $moduleDataSetup;

    public function __construct(
        EavSetupFactory          $eavSetupFactory,
        AttributeSetFactory      $attributeSetFactory,
        CategorySetupFactory     $categorySetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

//        Renaming the Coke Europe attribute set to "Personalization"
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, "Coke Europe");
        $attributeSet->load($attributeSetId);
        $attributeSet->setAttributeSetName('Personalization');
        $attributeSet->validate();
        $attributeSet->save();

//        Renaming the Coke Europe Simple attribute set to "Merchandise"
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, "Coke Europe Simple");
        $attributeSet->load($attributeSetId);
        $attributeSet->setAttributeSetName('Merchandise');
        $attributeSet->validate();
        $attributeSet->save();
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
