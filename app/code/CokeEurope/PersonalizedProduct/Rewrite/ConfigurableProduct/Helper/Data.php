<?php

namespace CokeEurope\PersonalizedProduct\Rewrite\ConfigurableProduct\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Data extends \Magento\ConfigurableProduct\Helper\Data
{
    /**
     * @param $currentProduct
     * @param $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        $allowAttributes = $this->getAllowAttributes($currentProduct);

        $patternProduct = [];

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                if ($product->isSalable()) {
                    $options[$productAttributeId][$attributeValue][] = $productId;

                    if ($product->getData('pattern')) {
                        $patternProduct[$productId] = $product->getPattern();
                    }

                }
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }

        /* EDC-944 Sort all product lists */
        $pattern = $this->getPatternAttribute($currentProduct);

        /* EDC-944:  Check if Pattern is used */
        if (count($pattern) === 0) {
            return $options;
        }

        $pattern = array_pop($pattern);
        $patternOptionsOrdering = array_flip(
            array_map(function($option) { return $option->getValue(); }, $pattern->getOptions())
        );

        // Lists to sort
        foreach ($options as $key => &$option) {
            if ($key === 'index') {
                continue;
            }
            foreach ($option as &$productList) {
                usort($productList, function($a, $b) use ($patternOptionsOrdering, $patternProduct) {
                    return $patternOptionsOrdering[$patternProduct[$a]] <=> $patternOptionsOrdering[$patternProduct[$b]];
                });
            }
        }

        return $options;
    }

    /**
     * Check if the configurable uses pattern, we'll sort the products list then.
     *
     * @param $product
     * @return array
     */
    protected function getPatternAttribute($product)
    {
        if ($product->getTypeId() !== Configurable::TYPE_CODE) {
            return [];
        }

        return array_filter(
            $product->getTypeInstance()->getUsedProductAttributes($product),
            function($attribute) {
                return $attribute->getAttributeCode() == 'pattern';
            });
    }
}
