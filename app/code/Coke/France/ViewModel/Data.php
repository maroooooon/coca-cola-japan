<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Coke\France\ViewModel;

use Coke\France\Helper\Config;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

class Data implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $configHelper;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var Category
     */
    private $categoryHelper;
    /**
     * @var Configurable
     */
    private $configurable;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var Image
     */
    private $imageHelper;


    /**
     * Data constructor.
     * @param Config $configHelper
     * @param StoreManagerInterface $storeManager
     * @param Category $categoryHelper
     * @param CategoryRepository $categoryRepository
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Image $imageHelper
     */
    public function __construct(
        Config $configHelper,
        StoreManagerInterface $storeManager,
        Category $categoryHelper,
        CategoryRepository $categoryRepository,
        CategoryCollectionFactory $categoryCollectionFactory,
        Configurable $configurable,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        Image $imageHelper
    )
    {
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getHeaderLinks(): array
    {
        $categories = $this->categoryHelper->getStoreCategories(false, false, true);
        $links = [];
        foreach ($categories as $category) {
            $catObj = $this->categoryRepository->get($category->getId());
            $links[] = [
                'id' => $category->getId(),
                'title' => $catObj->getName(),
                'target' => $catObj->getUrl(),
                'dropdown' => $category->hasChildren()
            ];
        }
        return $links;
    }

    /**
     * @param $id
     * @return null
     */
    public function getImageUrl($id)
    {
        try {
            return $this->categoryRepository->get($id)->getImageUrl();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    public function getParentProduct($id)
    {
        $parent = null;
        $parentIds = $this->configurable->getParentIdsByChild($id);
        if (isset($parentIds[0])) {
            $parent = $this->productRepository->getById( $parentIds[0]);
        }
        return $parent;
    }

    public function getPrimaryBottle()
    {
        return $this->productRepository->get($this->configHelper->primaryBottleSku());
    }

    public function getLevelTwo(): array
    {
        $categories = $this->categoryHelper->getStoreCategories(false,false,true);
        $links = [];
        foreach ($categories as $category) {
            if($category->hasChildren()) {
                $catObj = $this->categoryRepository->get($category->getId());
                foreach ($catObj->getChildrenCategories() as $child) {
                    $links[] = [
                        'id' => $child->getId(),
                        'title' => $child->getName(),
                        'target' => $child->getUrl(),
                        'children' => $child->getChildrenCategories()
                    ];
                }
            }
        }
        return $links;
    }

    public function getCansCategoryId(): ?int
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('name', 'Editions limitées')->setPageSize(1);
        if ($collection->getSize()) {
            return (int) $collection->getFirstItem()->getId();
        }
        return null;
    }

    public function getCansForCarousel(): array
    {
        $items = [];
        $collection = $this->productCollectionFactory->create();
        /* Filter collection by category id */
        if ($this->getCansCategoryId()) {
            $collection->addCategoriesFilter(['eq' => $this->getCansCategoryId()]);
        }
        $collection->addAttributeToSelect(['id','name','url_key','used_product_ids']);
        foreach ($collection as $item) {
            $children = $item->getTypeInstance()->getUsedProductIds($item) ?? [];
            $carouselItem = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'url' => $item->getUrlKey()
            ];
            /* Add child images */
            foreach ($children as $child) {
                /* load the product to get brand and image */
                $product = $this->productRepository->getById($child);
                $imgUrl = $this->imageHelper->init($product, 'carousel_image')->getUrl();
                $imgType = $product->getResource()->getAttribute('brand_swatch')->getFrontend()->getValue($product);
                /* add image to carousel item */
                $carouselItem['images'][] = [
                    'url' => $imgUrl,
                    'type' => str_replace(' ', '-', strtolower($imgType))
                ];
            }
            $items[] = $carouselItem;
        }
        return $items;
    }
    public function getCanTypes(): array
    {
        $items = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('type_id', array('eq' => 'configurable'));
        /* Filter collection by category id */
        if ($this->getCansCategoryId()) {
            $collection->addCategoriesFilter(['eq' => $this->getCansCategoryId()]);
        }
        $collection->addAttributeToSelect('*');
        foreach ($collection as $item) {
            $product = $this->productRepository->getById($item->getId());
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            foreach($attributes as $attribute) {
                foreach($attribute['values'] as $att) {
                    $items[] = [
                        'label' => $att['store_label'],
                        'value' => $att['value_index']
                    ];
                }
            }
        }
        return array_unique($items,SORT_REGULAR);
    }
    public function getCanSkus(): array
    {
        $skus = [];
        $collection = $this->productCollectionFactory->create();
        /* Filter collection by category id */
        if ($this->getCansCategoryId()) {
            $collection->addCategoriesFilter(['eq' => $this->getCansCategoryId()]);
        }
        $collection->addAttributeToFilter('status', ['eq' => 1])
            ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
            ->addAttributeToSelect('sku');
        foreach ($collection as $item) {
            $skus[] = $item->getSku();
        }
        return $skus;
    }
}

