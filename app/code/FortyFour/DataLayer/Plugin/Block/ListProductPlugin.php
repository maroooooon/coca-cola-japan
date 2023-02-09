<?php

namespace FortyFour\DataLayer\Plugin\Block;

class ListProductPlugin extends AbstractPlugin
{
    /**
     * @var string
     */
    private $template = 'FortyFour_DataLayer::product/list.phtml';

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param $result
     * @return string
     */
    public function afterGetTemplate(\Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        if (!$this->config->isBrandDataLayerEnabled()) {
            return $result;
        }

        return $this->template;
    }
}
