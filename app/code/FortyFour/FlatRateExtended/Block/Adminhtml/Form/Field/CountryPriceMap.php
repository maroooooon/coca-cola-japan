<?php

namespace FortyFour\FlatRateExtended\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

class CountryPriceMap extends AbstractFieldArray
{
    /**
     * @var Textarea
     */
    private $_textareaRenderer;

    /**
     * @var Country
     */
    private $_countryRenderer;

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('country', [
            'label' => __('Country'),
            'class' => 'required-entry',
            'renderer' => $this->_getCountryRenderer()
        ]);
        $this->addColumn('price', [
            'label' => __('Price'),
            'class' => 'required-entry validate-number validate-zero-or-greater',
            'renderer'=>$this->_getTextinputRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return Country|BlockInterface
     * @throws LocalizedException
     */
    protected function _getCountryRenderer()
    {
        if (!$this->_countryRenderer) {
            $this->_countryRenderer = $this->getLayout()->createBlock(
                Country::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => false
                    ]
                ]
            );
        }

        return $this->_countryRenderer;
    }

    /**
     * @return Textarea|BlockInterface
     * @throws LocalizedException
     */
    protected function _getTextinputRenderer()
    {
        if (!$this->_textareaRenderer) {
            $this->_textareaRenderer = $this->getLayout()->createBlock(
                Textinput::class,
                ''
            );
        }

        return $this->_textareaRenderer;
    }
}
