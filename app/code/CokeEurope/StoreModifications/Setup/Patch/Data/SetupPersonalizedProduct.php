<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\OptionFactory as ProductOptionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory as ConfigurableAttributeFactory;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\Entity\Attribute\OptionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class SetupPersonalizedProduct implements DataPatchInterface
{
    /** @var State  */
    protected State $state;

    /** @var ModuleDataSetupInterface  */
    protected ModuleDataSetupInterface $moduleDataSetup;

    /** @var CategorySetupFactory  */
    protected CategorySetupFactory $categorySetupFactory;

    /** @var ProductRepositoryInterface  */
    protected ProductRepositoryInterface $productRepository;

    /** @var ProductCollectionFactory  */
    protected ProductCollectionFactory $productCollectionFactory;

    /** @var ProductOptionFactory  */
    protected ProductOptionFactory $productOptionFactory;

    /** @var ProductFactory  */
    protected ProductFactory $productFactory;

    /** @var AttributeRepository  */
    protected AttributeRepository $attributeRepository;

    /** @var AttributeOptionManagementInterface  */
    protected AttributeOptionManagementInterface $attributeOptionManagement;

    /** @var OptionFactory  */
    protected OptionFactory $optionFactory;

    /** @var StoreManagerInterface  */
    protected StoreManagerInterface $storeManager;

    /** @var ConfigurableAttributeFactory  */
    protected ConfigurableAttributeFactory $configurableAttributeFactory;

    /** @var SearchCriteriaBuilder  */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /** @var Configurable  */
    protected Configurable $configurableProductType;

    /** @var LoggerInterface  */
    protected LoggerInterface $logger;

    /** @var int */
    protected int $productEntityTypeId;

    /** @var int */
    protected int $productAttributeSetId;

    /**
     * @param State $state
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductOptionFactory $productOptionFactory
     * @param AttributeRepository $attributeRepository
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param OptionFactory $optionFactory
     * @param StoreManagerInterface $storeManager
     * @param ConfigurableAttributeFactory $configurableAttributeFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Configurable $configurableProductType
     * @param LoggerInterface $logger
     */
    public function __construct(
        State $state,
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory,
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductOptionFactory $productOptionFactory,
        AttributeRepository $attributeRepository,
        AttributeOptionManagementInterface $attributeOptionManagement,
        OptionFactory $optionFactory,
        StoreManagerInterface $storeManager,
        ConfigurableAttributeFactory $configurableAttributeFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Configurable $configurableProductType,
        LoggerInterface $logger
    ) {
        $this->state = $state;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionFactory = $optionFactory;
        $this->storeManager = $storeManager;
        $this->configurableAttributeFactory = $configurableAttributeFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configurableProductType = $configurableProductType;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        // If Admin, products will be disabled for not having the right permissions
	    try {
		    $this->state->setAreaCode(Area::AREA_CRONTAB);
	    } catch (\Exception $e) {}

        /** @var CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->productEntityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $this->productAttributeSetId = $categorySetup->getAttributeSetId($this->productEntityTypeId, "Personalization");

        $this->addMerchantSkuAttribute($categorySetup);

        $variations = $this->gatherProductsAndLayoutIds();

        $variationProducts = $this->createAllVariations($variations);

        $configurable = $this->createPersonalizedProduct();
        $configurable = $this->setPersonalizedProductData($configurable);

        $this->assignAssociatedProducts($configurable, $variationProducts);
    }

    /**
     * Using all variations from Enable, create all simple products / store related informaation
     *
     * @param array $variations
     * @return array|Collection|\Magento\Framework\Data\Collection\AbstractDb
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createAllVariations(array $variations): Collection
    {
        if (count($variations) === 0) {
            return [];
        }

        $createdSkus = [];

        foreach ($variations as $variation) {
            $sku = $this->generateSkuVariation($variation);

            try {
                $product = $this->productRepository->get($sku);
            } catch (NoSuchEntityException $e) {
                /** @var Product $product */
                $product = $this->productFactory->create();
            }

            $storeId = $this->getStoreId($variation['MarketCode'], $variation['LangCode']);

            // If the product already exists, we're only updating the store specific information
            if (!$product->getId()) {
                // Our Brand selector is created using Brand + Flavour
                $brand = sprintf('%s %s', $variation['Brand'], $variation['Flavour']);
                $brandId = $this->getOrCreateAttributeOptionId('brand_swatch', $brand);
                $vesselId = $this->getOrCreateAttributeOptionId('package_bev_type', $variation['Vessel']);
                $patternId = $this->getOrCreateAttributeOptionId('pattern', $variation['ThemeDesc']);

                $product
                    ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
                    ->setSku($sku)
                    ->setAttributeSetId($this->productAttributeSetId)
                    ->setWeight(1)
                    ->setStatus(Status::STATUS_ENABLED)
                    ->setName(__('%1', $variation['SKUDesc'])->render())
                    ->setDescription($variation['SKUDesc'])
                    ->setBrandSwatch($brandId)
                    ->setPackageBevType($vesselId)
                    ->setPattern($patternId)
                    ->setUrlKey($sku)
                    ->setMetaTitle($variation['SKUDesc'])
                    ->setMetaKeywords('')
                    ->setMetaDescription(__('%1 - Pattern: %2', $variation['SKUDesc'], $variation['ThemeDesc'])->render())
                    ->setWebsiteIds([9,12]);

                // I know this is deprecated, but we haven't saved the product yet.
                $product->setStockData(
                    [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => 1,
                        'qty' => 99999
                    ]
                );

                try {
                    $product = $this->productRepository->save($product); // Or else updating the attribute will fail
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }

                $createdSkus[] = $sku;
            }

            // Store specific attributes
            if ($storeId) {
                $product->addAttributeUpdate('coke_layout_id', $variation['LayoutID'], $storeId);
                $product->addAttributeUpdate('merchant_sku', $variation['SKUID'], $storeId);

                // For BE-FR only, because NL does not exist for Layout IDs
                if ($variation['MarketCode'] === 'BE') {
                    $storeId = $this->getStoreId('BE', 'FR');
                    $product->addAttributeUpdate('coke_layout_id', $variation['LayoutID'], $storeId);
                    $product->addAttributeUpdate('merchant_sku', $variation['SKUID'], $storeId);
                }
            }
        }

        $createdSkus = array_unique($createdSkus);

        return $this->productCollectionFactory->create()->addFieldToFilter('sku', ['in' => $createdSkus]);
    }

    /**
     * Pulls JSON list of Layout IDs from Enable
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function gatherProductsAndLayoutIds()
    {
        $jsonUrl = 'https://prizehandling.com/ProductOrderingAPI/22083LayoutIDs.php';
        $request = new \Zend_Http_Client($jsonUrl);
        $request->setAdapter(new \Magento\Framework\HTTP\Adapter\Curl());

        try {
            $response = $request->request(\Zend_Http_Client::GET);
        } catch (\Exception $e) {
            $this->logger->error('Could not pull information about Layout IDs');
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @return ProductInterface|Product
     */
    public function createPersonalizedProduct()
    {
        try {
            $product = $this->productRepository->get('personalized-product');
        } catch (NoSuchEntityException $e) {
            $product = $this->productFactory->create();
        }

        return $product;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function setPersonalizedProductData(ProductInterface $product): ProductInterface
    {
        $product
            ->setTypeId('configurable')
            ->setAttributeSetId($this->productAttributeSetId)
            ->setName('Personalized Product')
            ->setSku('personalized-product')
            ->setWeight(1)
            ->setStatus(Status::STATUS_ENABLED)
            ->setDescription('This is Coca-Cola Original, taste the feeling')
            ->setUrlKey('personalized-product')
            ->setPageLayout('personalized-product')
            ->setMetaTitle('Coke Europe Personalized Product')
            ->setMetaKeywords('')
            ->setMetaDescription('Personalization Product')
            ->setWebsiteIds([9,12]);

        $product->setStockData(
            [
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
            ]
        );

        // Setup options
        $options = [
            [
                'sort_order' => 0,
                'title' => 'Message',
                'price_type' => 'fixed',
                'price' => '0',
                'type' => 'field',
                'is_require' => 1
            ],
            [
                'sort_order' => 1,
                'title' => 'Name',
                'price_type' => 'fixed',
                'price' => '0',
                'type' => 'field',
                'max_characters' => '7',
                'is_require' => 1
            ]
        ];

        $product->setHasOptions(1);
        $product->setCanSaveCustomOptions(true);

        $product = $this->productRepository->save($product);

        foreach ($options as $arrayOption) {
            $option = $this->productOptionFactory->create()
                ->setProductId($product->getRowId())
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
            $option->save();
            $product->addOption($option);
        }

        return $product;
    }

    /**
     *
     * @param Product $configurableProduct
     * @param Collection $childProducts
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function assignAssociatedProducts(
        Product $configurableProduct,
        Collection $childProducts
    ): void {
        // Three configurable attributes:  brand_swatch, package_bev_type, pattern
        $attributes = $this->attributeRepository->getList(
            Product::ENTITY,
            $this->searchCriteriaBuilder
                ->addFilter('attribute_code', ['brand_swatch', 'package_bev_type', 'pattern'], 'in')
                ->create()
        )->getItems();

        $attributeIds = array_map(
            function($attribute) {
                return $attribute->getAttributeId();
            },
            $attributes
        );

        $associatedProductIds = $childProducts->getAllIds();
        $position = 0;

        // Generate Configurable Attributes
        foreach ($attributeIds as $attributeId) {
            $configAttribute = $this->configurableAttributeFactory->create();
            $configAttribute->setAttributeId($attributeId);
            $configAttribute->setProductId($configurableProduct->getData('row_id')); // Why do I need to do this manually? What happened, EE?
            $configAttribute->setPosition($position);

            try {
                $configAttribute->save();
            } catch (\Exception $e) {
                // The only area this has failed is if the configurable product already exists.
                $this->logger->warning($e->getMessage());
            }

            $position++;
        }

        // At this point we need to reload the product since our used options have changed.
        $configurableProduct = $this->productRepository->get('personalized-product');

        $this->configurableProductType->setUsedProductAttributes($configurableProduct, $attributeIds);

        $configurableProduct->setTypeId('configurable'); // It should already be, but just in case? :)
        $configurableProduct->setNewVariationsAttributeSetId($this->productAttributeSetId);
        $configurableProduct->setAssociatedProductIds($associatedProductIds);
        $configurableProduct->setCanSaveConfigurableAttributes(true);

        $this->productRepository->save($configurableProduct);
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [
	        \CokeEurope\StoreModifications\Setup\Patch\Data\UpdateProductAttributesV1::class
        ];
    }

    /**
     * The rule to generate a unique SKU for each variation will be here
     *
     * @param array $variation
     * @return string
     */
    protected function generateSkuVariation(array $variation): string
    {
        return sprintf('%s-%s-%s-%s', $variation['Brand'], $variation['Flavour'], $variation['Vessel'], $variation['ThemeDesc']);
    }

    /**
     * Either creates the option and retrieves the option id or simply returns the option id
     * The repository implementation of get() already covers retrieving a loaded attribute multiple times and caches it
     *
     * @param string $attributeCode
     * @param string $optionLabel
     * @return string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function getOrCreateAttributeOptionId(string $attributeCode, string $optionLabel): string
    {
        $attribute = $this->attributeRepository->get($this->productEntityTypeId, $attributeCode);
        $options = $this->attributeOptionManagement->getItems($this->productEntityTypeId, $attributeCode);
        $optionId = $attribute->getSource()->getOptionId($optionLabel);
        $optionLabel = trim($optionLabel);

        if (!$optionId) {
            /** @var AttributeOptionInterface $newOption */
            $newOption = $this->optionFactory->create();
            $newOption->setLabel($optionLabel);
            $newOption->setSortOrder(0);
            $this->attributeOptionManagement->add($this->productEntityTypeId, $attributeCode, $newOption);
            $optionId = $attribute->getSource()->getOptionId($optionLabel);
        }

        return $optionId;
    }

    /**
     * @param CategorySetup $categorySetup
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function addMerchantSkuAttribute(CategorySetup $categorySetup): void
    {
        $categorySetup->addAttribute(
            Product::ENTITY,
            'merchant_sku',
            [
                'type' => 'varchar',
                'label' => 'Merchant Sku',
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
                'group' => 'General',
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
            ]
        );
    }

    /**
     * Given a Market Code and Language Code (from the LayoutID), return a Magento Store ID
     *
     * @param string $marketCode
     * @param string $languageCode
     * @return int|null
     */
    protected function getStoreId(string $marketCode, string $languageCode): ?int
    {
        $key = sprintf('%s-%s', $marketCode, $languageCode);
        $stores = [
            'BE-NL' => 'belgium_dutch',
            'BE-FR' => 'belgium_french',
            'DE-DE' => 'germany_german',
            'FR-FR' => 'france_french',
            'IE-EN' => 'ireland_english',
            'NL-NL' => 'netherlands_dutch',
            'NI-EN' => 'northern_ireland_english',
            'GB-EN' => 'great_britain_english',
        ];

        if (!isset($stores[$key])) {
            return null;
        }

        try {
            $storeId = $this->storeManager->getStore($stores[$key])->getId();
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(__('Store for %1 not found', $key));
            return null;
        }

        return $storeId;
    }
}
