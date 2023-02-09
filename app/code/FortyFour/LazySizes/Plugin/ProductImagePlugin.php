<?php

namespace FortyFour\LazySizes\Plugin;

use Magento\Catalog\Block\Product\Image;

class ProductImagePlugin extends AbstractPlugin
{
    /**
     * @param Image $subject
     * @param $template
     * @return string|string[]
     */
    public function beforeSetTemplate(
        Image $subject,
        $template
    ) {
        if (!$this->lazySizesConfig->isCatalogLazyLoadingEnabled()) {
            return [$template];
        }

        $template = str_replace('Magento_Catalog', 'FortyFour_LazySizes', $template);
        return [$template];
    }
}
