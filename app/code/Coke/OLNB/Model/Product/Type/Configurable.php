<?php

namespace Coke\OLNB\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Collection\SalableProcessor;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Config;

class Configurable extends \Magento\ConfigurableProduct\Model\Product\Type\Configurable
{
    /**
     * @var ProductAttributeRepositoryInterface|null
     */
    private $productAttributeRepository;
    /**
     * @var SearchCriteriaBuilder|null
     */
    private $searchCriteriaBuilder;
    /**
     * @var array
     */
    private $catalogConfig;

    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory,
        AttributeFactory $configurableAttributeFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        ?\Magento\Framework\Cache\FrontendInterface $cache = null,
        ?\Magento\Customer\Model\Session $customerSession = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        ProductInterfaceFactory $productFactory = null,
        SalableProcessor $salableProcessor = null,
        ?ProductAttributeRepositoryInterface $productAttributeRepository = null,
        ?SearchCriteriaBuilder $searchCriteriaBuilder = null
    ) {
        parent::__construct($catalogProductOption, $eavConfig, $catalogProductType, $eventManager, $fileStorageDb, $filesystem, $coreRegistry, $logger, $productRepository, $typeConfigurableFactory, $eavAttributeFactory, $configurableAttributeFactory, $productCollectionFactory, $attributeCollectionFactory, $catalogProductTypeConfigurable, $scopeConfig, $extensionAttributesJoinProcessor, $cache, $customerSession, $serializer, $productFactory, $salableProcessor, $productAttributeRepository, $searchCriteriaBuilder);
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Returns array of sub-products for specified configurable product
     * Result array contains all children for specified configurable product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $requiredAttributeIds Attributes to include in the select; one-dimensional array
     * @return ProductInterface[]
     */
    public function getUsedProducts($product, $requiredAttributeIds = null)
    {
        if (!$product->hasData($this->_usedProducts)) {
            $collection = $this->getConfiguredUsedProductCollection($product, false, $requiredAttributeIds);
            $usedProducts = array_values($collection->getItems());
            $product->setData($this->_usedProducts, $usedProducts);
        }

        return $product->getData($this->_usedProducts);
    }

    /**
     * Prepare collection for retrieving sub-products of specified configurable product
     * Retrieve related products collection with additional configuration
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $skipStockFilter
     * @param array $requiredAttributeIds Attributes to include in the select
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getConfiguredUsedProductCollection(
        \Magento\Catalog\Model\Product $product,
        $skipStockFilter = true,
        $requiredAttributeIds = null
    ) {
        $collection = $this->getUsedProductCollection($product);

        if ($skipStockFilter) {
            $collection->setFlag('has_stock_status_filter', true);
        }

        $attributesForSelect = $this->getAttributesForCollection($product);
        if ($requiredAttributeIds) {
            $this->searchCriteriaBuilder->addFilter('attribute_id', $requiredAttributeIds, 'in');
            $requiredAttributes = $this->productAttributeRepository
                ->getList($this->searchCriteriaBuilder->create())->getItems();
            $requiredAttributeCodes = [];
            foreach ($requiredAttributes as $requiredAttribute) {
                $requiredAttributeCodes[] = $requiredAttribute->getAttributeCode();
            }
            $attributesForSelect = array_unique(array_merge($attributesForSelect, $requiredAttributeCodes));
        }
        $collection
            ->addAttributeToSelect($attributesForSelect)
//            ->addFilterByRequiredOptions()
            ->setStoreId($product->getStoreId());

        $collection->addMediaGalleryData();
        $collection->addTierPriceData();

        return $collection;
    }

    /**
     * @return array
     */
    private function getAttributesForCollection(\Magento\Catalog\Model\Product $product)
    {
        $productAttributes = $this->getCatalogConfig()->getProductAttributes();

        $requiredAttributes = [
            'name',
            'price',
            'weight',
            'image',
            'thumbnail',
            'status',
            'visibility',
            'media_gallery'
        ];

        $usedAttributes = array_map(
            function($attr) {
                return $attr->getAttributeCode();
            },
            $this->getUsedProductAttributes($product)
        );

        return array_unique(array_merge($productAttributes, $requiredAttributes, $usedAttributes));
    }

    /**
     * Get Config instance
     * @return Config
     * @deprecated 100.1.0
     */
    private function getCatalogConfig()
    {
        if (!$this->catalogConfig) {
            $this->catalogConfig = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->catalogConfig;
    }
}