<?php

namespace Coke\Faq\Block\Adminhtml\Category\Edit;

class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;    
    
    /**
     * Constructor
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param []                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,    
        array $data = []
    )
    {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        // Call parent constructor
        parent::_construct();
        
        // Set form ID
        $this->setId('faq_category_form');
        
        // Set title
        $this->setTitle(__('FAQ Category Information'));
    }

    /**
     * Prepare form, add fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        // Get FAQ Category model
        $model = $this->_coreRegistry->registry('faqcategory_model');
        
        // Basic form data
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form', 
                    'action' => $this->getData('action'), 
                    'method' => 'post', 
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setHtmlIdPrefix('faqcategory_');

        // Form lengend title
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('FAQ Category Information'), 
                'class' => 'fieldset-wide'
            ]
        );

        // ID input hidden
        if ($model->getEntityId()) {
            $fieldset->addField(
                'entity_id', 
                'hidden', 
                [
                    'name' => 'entity_id'
                ]
            );
        }

        // Name
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name', 
                'label' => __('Category name'), 
                'title' => __('Category name'), 
                'required' => true
            ]
        );

        // Active
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'is_active',
                'required' => true,
                'options'  => [
                    '1' => __('Enabled'), 
                    '0' => __('Disabled')
                ]
            ]
        );

        // URL Key
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key', 
                'label' => __('URL Key'), 
                'title' => __('URL Key'), 
                'required' => false
            ]
        );        
        
        // Sort Order
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order', 
                'label' => __('Sort Order'), 
                'title' => __('Sort Order'), 
                'required' => false
            ]
        );        
        
        // Store
        if (!$this->_storeManager->hasSingleStore()) {
            $field = $fieldset->addField(
                'store_id',
                'select',
                [
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'name' => 'store_id',
                    'required' => true,
                    'value' => $model->getStoreId(),
                    'values' => $this->_systemStore->getStoreValuesForForm()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }        
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
