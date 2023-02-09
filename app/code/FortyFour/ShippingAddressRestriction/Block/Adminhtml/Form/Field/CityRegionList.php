<?php

namespace FortyFour\ShippingAddressRestriction\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

class CityRegionList extends AbstractFieldArray
{
    /**
     * @var TextArea
     */
    private $_textareaRenderer;

    /**
     * @var TextInput
     */
    private $_textInputRenderer;

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('city', [
            'label' => __('City'),
            'class' => 'required-entry',
            'renderer' => $this->_getTextInputRenderer()
        ]);
        $this->addColumn('region', [
            'label' => __('Region/Province'),
            'class' => 'required-entry',
            'renderer' => $this->_getTextareaRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return Textarea|BlockInterface
     * @throws LocalizedException
     */
    protected function _getTextareaRenderer()
    {
        if (!$this->_textareaRenderer) {
            $this->_textareaRenderer = $this->getLayout()->createBlock(
                TextArea::class,
                ''
            );
        }

        return $this->_textareaRenderer;
    }

    /**
     * @return Textarea|BlockInterface
     * @throws LocalizedException
     */
    protected function _getTextInputRenderer()
    {
        if (!$this->_textInputRenderer) {
            $this->_textInputRenderer = $this->getLayout()->createBlock(
                TextInput::class,
                ''
            );
        }

        return $this->_textInputRenderer;
    }
}
