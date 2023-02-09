<?php

namespace Coke\OLNB\ViewModel;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Psr\Log\LoggerInterface;

/**
 * Class Resolutions
 *
 * @package Coke\OLNB\ViewModel
 */
class Resolutions implements ArgumentInterface
{
    const RESOLUTIONS_CATEGORY_NAME = 'Resolutions';

    /** @var string XML patch to resolution request form config */
    public const XML_PATCH_RESOLUTION_REQUEST_ENABLED = 'coke_contact/form_options/enable_resolution';

    private $stringsToRemove = [
        'Resolution Can: ',
    ];

    private $productCollcetion;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var array
     */
    private $configurableChildren = [];
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var array
     */
    private $resolutions;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CategoryHelper
     */
    private $categoryHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ProductList constructor.
     *
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RequestInterface $request
     * @param CategoryHelper $categoryHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        RequestInterface $request,
        CategoryHelper $categoryHelper,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->request = $request;
        $this->categoryHelper = $categoryHelper;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getConfigurableChildren(Product $product): array
    {
        if (!isset($this->configurableChildren[$product->getId()])) {
            $simpleIds = $product->getTypeInstance()
                ->getUsedProductIds($product);

            // Get first configurable's children
            $this->configurableChildren[$product->getId()] = $simples = $this->productCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in '=> $simpleIds])
                ->getData();
        }

        return $this->configurableChildren[$product->getId()];
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getResolutionsCategoryId()
    {
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        $store = $this->storeManager->getStore();
        $rootCategoryId = $store->getRootCategoryId();

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('name', self::RESOLUTIONS_CATEGORY_NAME)
            ->addFieldToFilter('path', ['like'=> "1/${rootCategoryId}/%"])
            ->getFirstItem();

        return $collection->getId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductCollection()
    {
        if ($this->productCollcetion) {
            return $this->productCollcetion;
        }

        return $this->productCollcetion = $this->categoryRepository->get($this->getResolutionsCategoryId())
            ->getProductCollection()
            ->addAttributeToSelect('name');
    }

    public function getSelectedResolution()
    {
        $requested = $this->request->getParam('sku');

        if (!$requested) {
            return null;
        }

        foreach ($this->getProductCollection() as $product) {
            $children = $this->getConfigurableChildren($product);
            $skus = array_column($children, 'sku');

            if (in_array($requested, $skus)) {
                return $product->getSku();
            }
        }

        return null;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResolutions()
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($this->resolutions[$storeId]) {
            return $this->resolutions[$storeId];
        }

        try {
            $productCollection = $this->getProductCollection();
            $resolutions = [];

            foreach ($productCollection as $product) {
                $resolutions[$product->getSku()] = str_replace($this->stringsToRemove, '', $product->getName());
            }

            $this->resolutions[$storeId] = $resolutions;
            return $this->resolutions[$storeId];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get Resolution category URL
     *
     * @return string
     */
    public function getResolutionCategoryUrl(): string
    {
        $url = '/';
        try {
            $categoryId = $this->getResolutionsCategoryId();
            $category = $this->categoryRepository->get($categoryId);
            $url = $this->categoryHelper->getCategoryUrl($category);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $url;
    }

    /**
     * Check if resolutions filed is enabled in the request form for a current store
     *
     * @return bool
     */
    public function isResolutionsFormEnabled(): bool
    {
        $result = false;
        try {
            $store = $this->storeManager->getStore();
            $result = (bool) $this->scopeConfig->getValue(
                self::XML_PATCH_RESOLUTION_REQUEST_ENABLED,
                ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }
}
