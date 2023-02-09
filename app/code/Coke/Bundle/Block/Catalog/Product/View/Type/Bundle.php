<?php

namespace Coke\Bundle\Block\Catalog\Product\View\Type;

class Bundle extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle
{
    /**
     * @var array
     */
    private $productJsonConfig;

    /**
     * @return mixed|string
     */
    public function getJsonConfig()
    {
        $currentProduct = $this->getProduct();
        if (!isset($this->productJsonConfig[$currentProduct->getId()])) {
            $this->productJsonConfig[$currentProduct->getId()] = parent::getJsonConfig();
        }

        return $this->productJsonConfig[$currentProduct->getId()];
    }
}
