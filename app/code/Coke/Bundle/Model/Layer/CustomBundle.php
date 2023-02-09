<?php

namespace Coke\Bundle\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\StateFactory;
use Magento\Catalog\Model\ResourceModel;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class CustomBundle extends \Magento\Catalog\Model\Layer
{
    /**
     * @var ResourceModel\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @param ContextInterface $context
     * @param StateFactory $layerStateFactory
     * @param CollectionFactory $attributeCollectionFactory
     * @param Product $catalogProduct
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StateFactory $layerStateFactory,
        CollectionFactory $attributeCollectionFactory,
        Product $catalogProduct,
        StoreManagerInterface $storeManager,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return Product\Collection|mixed
     */
    public function getProductCollection()
    {
        $productSku = $this->getCurrentProduct()->getSku();

        if (isset($this->_productCollections[$productSku])) {
            $collection = $this->_productCollections[$productSku];
        } else {
            $collection = $this->productCollectionFactory->create();
            $collection->addFieldToFilter('sku', ['eq' => $productSku]);
            $this->_productCollections[$productSku] = $collection;
        }

        return $collection;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    private function getCurrentProduct(): \Magento\Catalog\Model\Product
    {
        return $this->registry->registry('current_product');
    }
}
