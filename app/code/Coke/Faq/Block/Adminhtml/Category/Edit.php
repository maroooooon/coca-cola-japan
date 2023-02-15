<?php

namespace Coke\Faq\Block\Adminhtml\Category;

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
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Coke_Faq';

        parent::_construct();

        $confirm = __('Are you sure? Any items assigned to this category will also be deleted.');

        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->buttonList->update(
            'delete',
            'onclick',
            'deleteConfirm(\'' . $confirm . '\', \'' . $this->getDeleteUrl() . '\')'
        );
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        $categoryRegistry = $this->_coreRegistry->registry('faq_category');
        if ($categoryRegistry->getEntityId()) {
            $name = $this->escapeHtml($categoryRegistry->getName());

            return __("Edit FAQ Category '%1'", $name);
        } else {
            return __('Add FAQ Category');
        }
    }
}