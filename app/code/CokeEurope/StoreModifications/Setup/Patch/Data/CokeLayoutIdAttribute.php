<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;

class CokeLayoutIdAttribute implements DataPatchInterface
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
		CategorySetupFactory     $categorySetupFactory
	)
	{
		$this->moduleDataSetup = $moduleDataSetup;
		$this->categorySetupFactory = $categorySetupFactory;
	}

	public function apply()
	{
		/** @var CategorySetup $categorySetup */
		$categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
		$this->addCokeLayoutIdAttribute($categorySetup);
	}

	public function getAliases()
	{
		return [];
	}

	public static function getDependencies()
	{
		return [];
	}

	/**
	 * @param \Magento\Eav\Setup\EavSetup $eavSetup
	 * @param int $entityTypeId
	 * @param int $attributeSetId
	 * @param int $generalGroupId
	 *
	 * @return void
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @throws \Zend_Validate_Exception
	 */
	private function addCokeLayoutIdAttribute(
		\Magento\Eav\Setup\EavSetup $eavSetup
	): void
	{
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'coke_layout_id',
			[
				'type' => 'varchar',
				'label' => 'Coke Layout Id',
				'input' => 'text',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible' => true,
				'required' => false,
				'user_defined' => true,
				'default' => '',
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
	}
}
