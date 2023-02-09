<?php

namespace Coke\Bundle\Setup\Patch\Data;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddBundleAllowAddToCartOnList implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var State
     */
    private $state;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
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

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, "Marche");

        // Attribute Set Groups
        $general = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, "General");

        // Add attribute
        if (!$eavSetup->getAttribute('catalog_product', 'bundle_allow_add_to_cart_on_list')) {
            $eavSetup->addAttribute('catalog_product', 'bundle_allow_add_to_cart_on_list', [
                'type' => 'int',
                'label' => 'Allow Add to Cart on PDP',
                'input' => 'boolean',
                'required' => 0,
                'user_defined' => 1,
                'default' => 0,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1,
                'apply_to' => 'bundle',
            ]);

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $general,
                'bundle_allow_add_to_cart_on_list',
                null
            );
        }


        // Modify some products.
        /** @var Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $products = $productCollection->addFieldToFilter('sku', ['like' => 'SKU-Pre-Bundle-%'])
            ->addFieldToFilter('type_id', 'bundle')
            ->getItems();

        /** @var ProductInterface $product */
        $attributeId = $eavSetup->getAttributeId($entityTypeId, 'bundle_allow_add_to_cart_on_list');
        foreach ($products as $product) {
            $this->moduleDataSetup->getConnection()->insertOnDuplicate(
                'catalog_product_entity_int',
                ['attribute_id' => $attributeId, 'store_id' => 0, 'row_id' => $product->getData('row_id'), 'value' => '1'],
                ['value']
            );
        }
    }
}
