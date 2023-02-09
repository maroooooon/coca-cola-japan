<?php

namespace FortyFour\DataLayer\Plugin\Block;

class ProductViewPlugin extends AbstractPlugin
{
    /**
     * @var string
     */
    private $template = 'FortyFour_DataLayer::product/view/addtocart.phtml';

    /**
     *
     */
    public function afterGetTemplate(\Magento\Catalog\Block\Product\View $subject, $result)
    {
        if (!$this->config->isBrandDataLayerEnabled() || $result != 'Magento_Catalog::product/view/addtocart.phtml') {
            return $result;
        }

        return $this->template;
    }
}
