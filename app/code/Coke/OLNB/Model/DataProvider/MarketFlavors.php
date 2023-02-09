<?php

namespace Coke\OLNB\Model\DataProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class MarketFlavors
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * MarketFlavors constructor.
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        /** @var Product $firstConfigurable */
        $firstConfigurable = $this->productCollectionFactory->create()
            ->addStoreFilter()
            ->addFieldToFilter(ProductInterface::TYPE_ID, Configurable::TYPE_CODE)
            ->setVisibility([Visibility::VISIBILITY_BOTH])
            ->setPage(1, 1)
            ->getFirstItem();

        if (!$firstConfigurable->getId()) {
            return [];
        }

        $simpleIds = $firstConfigurable->getTypeInstance()
            ->getUsedProductIds($firstConfigurable);

        // Get first configurable's children
        $simples = $this->productCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in '=> $simpleIds])
            ->addAttributeToSelect('flavor')
            ->getItems();

        if (count($simples) === 0) {
            return [];
        }

        $attribute = current($simples)->getResource()->getAttribute('flavor');
        $brands = array_map(function (Product $simple) use ($attribute) {
            return $attribute->usesSource() ?
                $attribute->getSource()->getOptionText($simple->getData('flavor')) :
                null;
        }, $simples);

        return array_filter(array_values($brands));
    }
}