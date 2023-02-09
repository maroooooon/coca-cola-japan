<?php

namespace FortyFour\Voucher\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class TextInput extends AbstractFieldArray
{
    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $inputName = $this->getInputName();
        $columnName = $this->getColumnName();
        $column = $this->getColumn();

        return '<input type="text" id="' . $this->_getCellInputElementId(
                '<%- _id %>',
                $columnName
            ) . '"' . '" name="' . $inputName . '"' .
            ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
