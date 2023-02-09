<?php

namespace Coke\Bundle\Block\Sales\Order\Items;

class Renderer extends \Magento\Bundle\Block\Sales\Order\Items\Renderer
{
    /**
     * @param mixed $item
     * @return string
     */
    public function getValueHtml($item)
    {
        if ($attributes = $this->getSelectionAttributes($item)) {
            return  "<span class='hide-qty-left'>" . sprintf('%d', $attributes['qty']) . ' x ' . "</span>" . $this->escapeHtml($item->getName()) . " "
                . $this->getOrder()->formatPrice($attributes['price']);
        }
        return $this->escapeHtml($item->getName());
    }
}
