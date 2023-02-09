<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;

class PlaceholderAndCustomizableSKUAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * PatchInitial constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function apply()
    {
        /** @var CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
	    $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
	    $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, "Personalization");
	    $generalGroupId = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, "General");

        $this->createIsPhrasePlaceholderAttribute($categorySetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->createCustomizableSkuAttribute($categorySetup, $entityTypeId, $attributeSetId, $generalGroupId);
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }

    protected function createIsPhrasePlaceholderAttribute($categorySetup, $entityTypeId, $attributeSetId, $generalGroupId)
    {
        $categorySetup->addAttribute(
           $entityTypeId,
            'is_phrase_placeholder',
            [
                'type' => 'int',
                'label' => 'Is Phrase Placeholder',
                'input' => 'boolean',
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
	            'default' => false
            ],
        );
	    $this->addAttributeToSetAndGroup(
		    $categorySetup,
		    $entityTypeId,
		    $attributeSetId,
		    $generalGroupId,
		    'is_phrase_placeholder'
	    );
    }

	protected function createCustomizableSkuAttribute($categorySetup, $entityTypeId, $attributeSetId, $generalGroupId)
	{
		$categorySetup->addAttribute(
			$entityTypeId,
			'customizable_sku',
			[
				'type' => 'varchar',
				'label' => 'Customizable SKU',
				'input' => 'text',
				'required' => false,
				'user_defined' => true,
				'searchable' => true,
				'filterable' => true,
				'comparable' => true,
				'visible_in_advanced_search' => true,
				'is_used_in_grid' => true,
				'is_visible_in_grid' => false,
				'is_filterable_in_grid' => true
			],
		);
		$this->addAttributeToSetAndGroup(
			$categorySetup,
			$entityTypeId,
			$attributeSetId,
			$generalGroupId,
			'customizable_sku'
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
}
