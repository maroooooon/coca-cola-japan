<?php

namespace FortyFour\Sales\Plugin;

use Exception;
use Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid;

class OrderCreateSearchGridPlugin
{
    /**
     * @throws Exception
     */
    public function beforeToHtml(
        Grid $subject
    ) {
        try {
            $subject->addColumnAfter(
                'brand',
                [
                    'header' => __('Brand'),
                    'sortable' => true,
                    'index' => 'brand',
                    'renderer' => \FortyFour\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Brand::class,
                    'header_css_class' => 'col-brand',
                    'column_css_class' => 'col-brand',
                    'filter' => false
                ],
                'sku'
            );
        } catch (\Exception $e) {
            // silence
        }
    }
}
