<?php

namespace Coke\Bundle\Model\Layer\CustomBundle;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Psr\Log\LoggerInterface;

class FilterableAttributeList implements FilterableAttributeListInterface
{
    const FILTERABLE_ATTRIBUTES = ['brand', 'container', 'product_category'];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FilterableAttributeList constructor
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Retrieve list of filterable attributes
     *
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getList()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection */
        $collection = $this->collectionFactory->create();
        $collection->setItemObjectClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)
            ->addStoreLabel($this->storeManager->getStore()->getId())
            ->setOrder('position', 'ASC');
        // $collection = $this->_prepareAttributeCollection($collection);
        $collection->addFieldToFilter(
            'main_table.attribute_code',
            ['in' => self::FILTERABLE_ATTRIBUTES]
        );
        $collection->load();

        return $collection;
    }

//    /**
//     * Add filters to attribute collection
//     *
//     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection
//     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
//     */
//    protected function _prepareAttributeCollection($collection)
//    {
//        $collection->addIsFilterableFilter();
//        return $collection;
//    }
}
