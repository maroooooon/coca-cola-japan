<?php

namespace Coke\Bundle\Model\Product;

use Coke\Bundle\Model\Layer\CustomBundle\FilterableAttributeList;
use Magento\Bundle\Model\ResourceModel\Selection\Collection as Selections;
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DataObject;

class Type extends \Magento\Bundle\Model\Product\Type
{
    public function prepareForCartAdvanced(
        DataObject $buyRequest,
        $product,
        $processMode = null
    )
    {
        /**
         * If the bundled product has the special attribute "bundle_allow_add_to_cart_on_list"
         * then we will auto add bundle options to the add to cart request so it skips over the redirect
         * to the PDP.
         */
        if ($product->getData('bundle_allow_add_to_cart_on_list')) {
            // Are there no bundled options in the request?
            if (!is_array($buyRequest->getBundleOption())) {
                // Is the option there?
                // Figure out what we should put here as the default options.
                $this->addDefaultBundleOptions($buyRequest, $product);
            }
        }

        return parent::prepareForCartAdvanced($buyRequest, $product, $processMode);
    }

    public function addDefaultBundleOptions(DataObject $buyRequest, Product $product)
    {
        // Load all the bundled option selections for the product.
        $options = $this->getOptionsIds($product);
        $selectionCollection = $this->_bundleCollection->create();
        $selectionCollection->setOptionIdsFilter($options);
        $selections = $selectionCollection->getItems();

        /** @var Selection $selection */
        $selected = [];
        foreach ($selections as $selection) {
            if ($selection->getIsDefault()) {
                // Format:
                // [
                //    "option_id" => [
                //      "selection_id" => "selection_id"
                //    ]
                // ]
                $selected[$selection->getOptionId()][$selection->getSelectionId()] = (string)$selection->getSelectionId();//(int)$selection->getSelectionQty();
            }
        }

        // Set the bundle selections in the buy request.
        $buyRequest->setBundleOption($selected);
    }

    public function isPossibleBuyFromList($product)
    {
        /**
         * Override this so the add to cart url on the frontend is correct.
         */
        return $product->getData('bundle_allow_add_to_cart_on_list') || !$this->hasRequiredOptions($product);
    }

    /**
     * This allows us to add bundle option products with a qty more than one.
     * Defaults back to 1 when this is not possible.
     * This disregards the getSelectionCanChangeQty on checkbox elements.
     *
     * @param \Magento\Framework\DataObject $selection
     * @param int[] $qtys
     * @param int $selectionOptionId
     * @return float|int
     */
    protected function getQty($selection, $qtys, $selectionOptionId)
    {
        if ($selection->getSelectionCanChangeQty() && isset($qtys[$selectionOptionId])) {
            $qty = (float)$qtys[$selectionOptionId] > 0 ? $qtys[$selectionOptionId] : 1;
        } elseif (isset($qtys[$selectionOptionId][$selection->getId()])) {
            $qty = (float)$qtys[$selectionOptionId][$selection->getId()] ? $qtys[$selectionOptionId][$selection->getId()] : 1;
        } else {
            $qty = (float)$selection->getSelectionQty() ? $selection->getSelectionQty() : 1;
        }
        $qty = (float)$qty;

        return $qty;
    }

    /**
     * Retrieve bundle selections collection based on used options
     *
     * @param array $optionIds
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Bundle\Model\ResourceModel\Selection\Collection
     */
    public function getSelectionsCollection($optionIds, $product)
    {
        $selectionsCollection = parent::getSelectionsCollection($optionIds, $product);

        if ($product->getSku() === 'custom-bundle') {
            $selectionsCollection = $this->addCustomBundleFilterableAttributes($selectionsCollection);
            $selectionsCollection = $this->sortCustomBundle($selectionsCollection);
        }

        return $selectionsCollection;
    }

    /**
     * @param \Magento\Bundle\Model\ResourceModel\Selection\Collection $collection
     * @return \Magento\Bundle\Model\ResourceModel\Selection\Collection|void
     */
    private function addCustomBundleFilterableAttributes(
        \Magento\Bundle\Model\ResourceModel\Selection\Collection $collection
    ) {
        if ((!is_array(FilterableAttributeList::FILTERABLE_ATTRIBUTES))
            || !count(FilterableAttributeList::FILTERABLE_ATTRIBUTES)) {
            return;
        }

        foreach (FilterableAttributeList::FILTERABLE_ATTRIBUTES as $attribute) {
            $collection->addAttributeToSelect($attribute);
        }

        return $collection;
    }

    /**
     * @param \Magento\Bundle\Model\ResourceModel\Selection\Collection $collection
     * @return \Magento\Bundle\Model\ResourceModel\Selection\Collection|void
     */
    private function sortCustomBundle(
        \Magento\Bundle\Model\ResourceModel\Selection\Collection $collection
    ) {
        $collection->getSelect()->reset(\Magento\Framework\DB\Select::ORDER);
        $collection->setOrder('brand', SortOrder::SORT_ASC);
        $collection->setOrder('container', SortOrder::SORT_ASC);
        $collection->setOrder('position', SortOrder::SORT_ASC);

        return $collection;
    }
}
