<?php

namespace Coke\FaqCustom\Block\Adminhtml\Item\Edit;

class Form
    extends \Coke\Faq\Block\Adminhtml\Item\Edit\Form
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
        array $data = [])
    {
        parent::__construct($context, $registry, $formFactory, $categoryRepository, $wysiwygConfig, $data);
    }

    /**
     * @return \Coke\Faq\Block\Adminhtml\Item\Edit\Form|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
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

        return \Magento\Backend\Block\Widget\Form\Generic::_prepareForm();
    }
}
