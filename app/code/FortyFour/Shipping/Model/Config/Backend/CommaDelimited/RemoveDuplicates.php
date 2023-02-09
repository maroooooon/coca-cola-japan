<?php

namespace FortyFour\Shipping\Model\Config\Backend\CommaDelimited;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

class RemoveDuplicates extends Value
{
    /**
     * @return RemoveDuplicates|void
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = explode(',', $value);
        $value = array_unique($value);
        $value = implode(',', $value);
        $this->setValue($value);
        return parent::beforeSave();
    }
}
