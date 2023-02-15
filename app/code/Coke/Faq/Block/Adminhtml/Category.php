<?php

namespace Coke\Faq\Block\Adminhtml;

class Category
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
        $this->_controller = 'adminhtml_category';
        $this->_headerText = __('FAQ Categories');
        $this->_addButtonLabel = __('Add New FAQ Category');
        parent::_construct();
    }
}