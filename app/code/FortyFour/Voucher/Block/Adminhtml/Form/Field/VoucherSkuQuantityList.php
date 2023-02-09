<?php

namespace FortyFour\Voucher\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

class VoucherSkuQuantityList extends AbstractFieldArray
{
    /**
     * @var TextInput
     */
    private $_textInputRenderer;

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $textInputColumns = [
            'sku' => [
                'label' => 'SKU',
                'class' => 'required-entry'
            ],
            'number_of_vouchers_to_send' => [
                'label' => 'Number of Vouchers to Send',
                'class' => 'required-entry validate-number validate-greater-than-zero',
            ]
        ];

        foreach ($textInputColumns as $key => $textInputColumn) {
            $this->addColumn($key, [
                'label' => $textInputColumn['label'],
                'class' => $textInputColumn['class'],
                'renderer' => $this->_getTextInputRenderer()
            ]);
        }

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return TextInput|BlockInterface
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
