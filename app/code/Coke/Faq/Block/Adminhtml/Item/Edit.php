<?php

namespace Coke\Faq\Block\Adminhtml\Item;
          
class Edit
    extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Class constructor
     * 
     * @param Context  $context
     * @param Registry $registry
     * @param array    $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class init
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'Coke_Faq';

        parent::_construct();

        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        $itemRegistry = $this->_coreRegistry->registry('faq_item');
        if ($itemRegistry->getEntityId()) {
            $title = $this->escapeHtml($itemRegistry->getTitle());

            return __("Edit FAQ Item '%1'", $title);
        } else {
            return __('Add FAQ Item');
        }
    }
}