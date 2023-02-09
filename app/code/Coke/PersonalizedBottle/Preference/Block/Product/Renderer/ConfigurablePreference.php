<?php

namespace Coke\PersonalizedBottle\Preference\Block\Product\Renderer;

class ConfigurablePreference extends \Magento\Swatches\Block\Product\Renderer\Configurable
{

    /**
     * Return renderer template
     *
     * Template for product with swatches is different from product without swatches
     *
     * @return string
     */
    protected function getRendererTemplate()
    {
        if ($this->getProduct()->getSku() != 'personalized-bottle') {
            return $this->isProductHasSwatchAttribute() ?
                self::SWATCH_RENDERER_TEMPLATE : self::CONFIGURABLE_RENDERER_TEMPLATE;
        }
        return 'Coke_PersonalizedBottle::product/view/renderer.phtml';
    }
}
