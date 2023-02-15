<?php

namespace Coke\Faq\Block\Adminhtml;

class Item
    extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor Class
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Coke_Faq';
        $this->_controller = 'adminhtml_item';
        $this->_headerText = __('FAQ Items');
        $this->_addButtonLabel = __('Add New FAQ Item');
        parent::_construct();
    }
}