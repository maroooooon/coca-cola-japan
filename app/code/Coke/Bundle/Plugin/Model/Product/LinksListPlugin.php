<?php

namespace Coke\Bundle\Plugin\Model\Product;

use Magento\Bundle\Api\Data\LinkInterfaceFactory;
use Magento\Bundle\Model\Product\Type;
use Magento\Framework\Api\DataObjectHelper;

class LinksListPlugin
{


    /**
     * @var LinkInterfaceFactory
     */
    private $linkFactory;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        LinkInterfaceFactory $linkFactory,
        Type $type,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->linkFactory = $linkFactory;
        $this->type = $type;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Bundle Product Items Data
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $optionId
     * @return \Magento\Bundle\Api\Data\LinkInterface[]
     */
    public function aroundGetItems(\Magento\Bundle\Model\Product\LinksList $subject, callable $proceed, \Magento\Catalog\Api\Data\ProductInterface $product, $optionId)
    {
        $selectionCollection = $this->type->getSelectionsCollection([$optionId], $product);

        $productLinks = [];
        /** @var \Magento\Catalog\Model\Product $selection */
        foreach ($selectionCollection as $selection) {
            $bundledProductPrice = $selection->getSelectionPriceValue();
            // This around plugin simply removes this
            /*if ($bundledProductPrice <= 0) {
                $bundledProductPrice = $selection->getPrice();
            }*/
            $selectionPriceType = $product->getPriceType() ? $selection->getSelectionPriceType() : null;

            $selectionPrice = $bundledProductPrice ? $bundledProductPrice : null;

            if ($bundledProductPrice === 0) {
                $selectionPrice = 0;
            }

            /** @var \Magento\Bundle\Api\Data\LinkInterface $productLink */
            $productLink = $this->linkFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productLink,
                $selection->getData(),
                \Magento\Bundle\Api\Data\LinkInterface::class
            );
            $productLink->setIsDefault($selection->getIsDefault())
                ->setId($selection->getSelectionId())
                ->setQty($selection->getSelectionQty())
                ->setCanChangeQuantity($selection->getSelectionCanChangeQty())
                ->setPrice($selectionPrice)
                ->setPriceType($selectionPriceType);
            $productLinks[] = $productLink;
        }
        return $productLinks;
    }
}
