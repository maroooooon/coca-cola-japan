<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class SetupCategoryAndProduct implements DataPatchInterface
{
    const SAMPLE_SKU = 'COKE-EUROPE-SAMPLE';

    protected State $appState;
    protected CategoryRepositoryInterface $categoryRepository;
    protected Category $resourceCategory;
    protected CategoryLinkManagementInterface $categoryLinkManagement;
    protected ProductRepositoryInterface $productRepository;
    protected ProductFactory $productFactory;
    protected LoggerInterface $logger;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param ProductFactory $productFactory
     */
    public function __construct(
        State $appState,
        CategoryRepositoryInterface $categoryRepository,
        Category $resourceCategory,
        ProductRepositoryInterface  $productRepository,
        CategoryLinkManagementInterface $categoryLinkManagement,
        ProductFactory $productFactory,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->categoryRepository = $categoryRepository;
        $this->resourceCategory = $resourceCategory;
        $this->productRepository = $productRepository;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
		try {
			$this->appState->setAreaCode(Area::AREA_ADMINHTML);
		} catch (\Exception $e) {}
        $category = $this->renameCategoryToCokeEurope();
        $sampleProduct = $this->createSampleProduct();
        $this->categoryLinkManagement->assignProductToCategories($sampleProduct->getSku(), [$category->getId()]);
    }

    public function renameCategoryToCokeEurope()
    {
        $category = $this->categoryRepository->get(45, 0); //Found in config, so yes, it's an int.
        $category->setName('Coke Europe');
        try {
            $this->resourceCategory->saveAttribute($category, 'name');
        } catch (\Exception $e) {
            $this->logger->error('Could not update OLNB Category to Coke Europe. Reason: ' . $e->getMessage());
        }

        return $category;
    }

    public function createSampleProduct()
    {
        try {
            $sampleProduct = $this->productRepository->get(SetupCategoryAndProduct::SAMPLE_SKU, false, 0);
            return $sampleProduct;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $sampleProduct = $this->productFactory->create();
            $sampleProduct->setSku('COKE-EUROPE-SAMPLE');
        }

        $sampleProduct
            ->setUrlKey('coke-europe-sample')
            ->setAttributeSetId(12)
            ->setStatus(Status::STATUS_ENABLED)
            ->setName('Coca-Cola')
            ->setPackageBevType(8298) //Bottle
            ->setPersonalizedLabelLocation(8300)
            ->setBrand(5471)
            ->setFlavor(5610)
            ->setNameCharacterLimit(18)
            ->setPrice(5.00)
            ->setPackSize(5544)
            ->setPackSizeQuantity(1)
            ->setContainer(5622)
            ->setDescription("This is Coca-Cola Original, taste the feeling")
            ->setShortDescription("Carbonated Water, Sugar, Colour (Caramel E150d), Phosphoric Acid, Natural Flavourings including Caffeine")
            ->setNutritionalEnglish("Energy: 180 kJ / 24 kcal – Fat: 0g – of which saturates: 0g – Carbohydrate: 10.6g – of which sugars: 10.6g – Protein: 0g – Salt 0g")
            ->setWebsiteIds([9,12]);

        $this->productRepository->save($sampleProduct);

        return $sampleProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
