<?php

namespace FortyFour\DataLayer\Plugin\Block;

use Magento\CatalogWidget\Block\Product\ProductsList;

class ListProductWidgetPlugin extends AbstractPlugin
{
    /**
     * @var string
     */
    private string $template = 'FortyFour_DataLayer::product/listwidget.phtml';

    /**
     * @param ProductsList $subject
     * @param $result
     * @return string
     */
    public function afterGetTemplate(ProductsList $subject, $result): string
    {
        if (!$this->config->isBrandDataLayerEnabled()) {
            return $result;
        }
        return $this->template;
    }
}
