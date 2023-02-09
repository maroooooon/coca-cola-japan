<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Eav\Api\AttributeSetRepositoryInterface;

class NewProductAttributes implements DataPatchInterface
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
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

	    $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, "Personalization");
        $generalGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "General");
        $contentGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "Content");

        $this->updateAttributeSetName($eavSetup, $entityTypeId, $attributeSetId);

        $this->addPackageBeverageTypeAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addLabelLocationAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addPhraseCollectionAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addPhraseAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addNameEnglishAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addNameCharacterLimitAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addPhraseCharacterLimitAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addPackSizeQuantityAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addDescriptionEnglishAttribute($eavSetup, $entityTypeId, $attributeSetId, $contentGroupId);
        $this->addNutritionalEnglishAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addIngredientListEnglishAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
        $this->addPersonalizedLabelAttribute($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
	    $this->addMissingAttributesToAttributeSet($eavSetup, $entityTypeId, $attributeSetId, $generalGroupId);
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
    private function addPackageBeverageTypeAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'package_bev_type',
            [
                'type' => 'int',
                'label' => 'Variant: Can or Bottle',
                'input' => 'select',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'option' => [
                    'values' => [
                        'Can',
                        'Plastic Bottle',
                        'Glass Bottle'
                    ]
                ],
            ]
        );
        $this->addAttributeToSetAndGroup(
            $eavSetup,
            $entityTypeId,
            $attributeSetId,
            $generalGroupId,
            'package_bev_type'
        );
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
    private function addLabelLocationAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'personalized_label_location',
            [
                'type' => 'int',
                'label' => 'Personalized Label Location',
                'input' => 'select',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'option' => [
                    'values' => [
                        'Neck',
                        'Body'
                    ]
                ],
            ]
        );
        $this->addAttributeToSetAndGroup(
            $eavSetup,
            $entityTypeId,
            $attributeSetId,
            $generalGroupId,
            'personalized_label_location'
        );
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
    private function addPhraseCollectionAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'phrase_collection',
            [
                'type' => 'int',
                'label' => 'Phrase Collection',
                'input' => 'select',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'option' =>
                    [
                        'values' => [
                            'Christmas',
                            'New Years',
                            'Valentine\'s Day'
                        ]
                    ]
            ]
        );
        $this->addAttributeToSetAndGroup(
            $eavSetup,
            $entityTypeId,
            $attributeSetId,
            $generalGroupId,
            'phrase_collection'
        );
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
    private function addPhraseAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'applicable_phrases',
            [
                'type' => 'int',
                'label' => 'Applicable Phrases',
                'input' => 'select',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
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
            'applicable_phrases'
        );
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
    private function addNameEnglishAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'name_english',
            [
                'type' => 'varchar',
                'label' => 'Name (English)',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
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
            'name_english'
        );
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
    private function addNameCharacterLimitAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'name_character_limit',
            [
                'type' => 'int',
                'label' => 'Name Character Limit',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
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
            'name_character_limit'
        );
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
    private function addPhraseCharacterLimitAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'phrase_character_limit',
            [
                'type' => 'int',
                'label' => 'Phrase Character Limit',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
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
            'phrase_character_limit'
        );
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
    private function addPackSizeQuantityAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pack_size_quantity',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Pack Size/Quantity',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
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
            'pack_size_quantity'
        );
    }

    /**
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param int $entityTypeId
     * @param int $attributeSetId
     * @param int $contentGroupId
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addDescriptionEnglishAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $contentGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'description_english',
            [
                'type' => 'text',
                'label' => 'Description (English)',
                'input' => 'textarea',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'is_wysiwyg_enabled'      => true,
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
            $contentGroupId,
            'description_english'
        );
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
    private function addNutritionalEnglishAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'nutritional_english',
            [
                'type' => 'text',
                'label' => 'Nutritional (English)',
                'input' => 'textarea',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'is_wysiwyg_enabled'      => true,
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
            'nutritional_english'
        );
    }


    /**
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param int $entityTypeId
     * @param int $attributeSetId
     * @param int $generalGroupId
     * @return void
     */
    private function addPersonalizedLabelAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'personalized_label',
            [
                'type' => 'text',
                'label' => 'Personalized Label',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
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
            'personalized_label'
        );
    }

    /**
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param int $entityTypeId
     * @param int $attributeSetId
     * @param int $generalGroupId
     * @return void
     */
    private function addIngredientListEnglishAttribute(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        int $entityTypeId,
        int $attributeSetId,
        int $generalGroupId
    ): void {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'ingredient_english',
            [
                'type' => 'text',
                'label' => 'Ingredients (English)',
                'input' => 'textarea',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'is_wysiwyg_enabled'      => true,
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
            'ingredient_english'
        );
    }

    /**
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param                             $entityTypeId
     * @param                             $attributeSetId
     *
     * @return void
     */
    private function updateAttributeSetName(\Magento\Eav\Setup\EavSetup $eavSetup, $entityTypeId, $attributeSetId): void
    {
        $eavSetup->updateAttributeSet($entityTypeId, $attributeSetId, 'attribute_set_name', 'Personalization');
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
	private function addMissingAttributesToAttributeSet(
		\Magento\Eav\Setup\EavSetup $eavSetup,
		int $entityTypeId,
		int $attributeSetId,
		int $generalGroupId
	): void {
		$this->addAttributeToSetAndGroup(
			$eavSetup,
			$entityTypeId,
			$attributeSetId,
			$generalGroupId,
			'brand_swatch'
		);
		$this->addAttributeToSetAndGroup(
			$eavSetup,
			$entityTypeId,
			$attributeSetId,
			$generalGroupId,
			'pattern'
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
