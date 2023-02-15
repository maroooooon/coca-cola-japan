<?php

namespace Coke\Faq\Block\Adminhtml\Item\Edit;

class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Coke\Faq\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->categoryRepository = $categoryRepository;
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
        $this->setId('faq_item_form');

        // Set title
        $this->setTitle(__('FAQ Item Information'));
    }

    /**
     * Prepare form, add fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('faqitem_model');

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

        $form->setHtmlIdPrefix('faqitem_');

        // Form lengend title
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('FAQ Item Information'),
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

        // Title
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );

        // Description
        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:10em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        //TODO: Hidden for future developments
        // Tags
//        $fieldset->addField(
//            'tags',
//            'text',
//            [
//                'name' => 'tags',
//                'label' => __('Tags'),
//                'title' => __('Tags'),
//                'required' => false
//            ]
//        );

        // Get categories from collection
        $categoriesSearch = $this->categoryRepository->getActiveCategories("ASC");

        $faqCategoriesOptions = [];
        foreach ($categoriesSearch->getItems() as $faqCategory) {
            $faqCategoriesOptions[$faqCategory['entity_id']] = $faqCategory['name'];
        }

        // Category
        $fieldset->addField(
            'faq_category_id',
            'select',
            [
                'label'    => __('FAQ Category'),
                'title'    => __('FAQ Category'),
                'name'     => 'faq_category_id',
                'required' => true,
                'options'  => $faqCategoriesOptions
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
                'required' => false,
                'disabled' => $model->getEntityId()
            ]
        );

        //TODO: Hidden for future developments
        // Most frequently
//        $fieldset->addField(
//            'most_frequently',
//            'select',
//            [
//                'label'    => __('Most Frequently'),
//                'title'    => __('Most Frequently'),
//                'name'     => 'most_frequently',
//                'required' => false,
//                'options'  => [
//                    '1' => __('Yes'),
//                    '0' => __('No')
//                ]
//            ]
//        );

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

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
