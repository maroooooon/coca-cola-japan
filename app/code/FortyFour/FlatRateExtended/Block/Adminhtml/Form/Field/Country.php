<?php

namespace FortyFour\FlatRateExtended\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Country extends Select
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $countrySource;

    /**
     * @param Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $countrySource
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Directory\Model\Config\Source\Country $countrySource,
        array $data = []
    ) {
        $this->countrySource = $countrySource;
        parent::__construct($context, $data);
    }
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions()
    {
        return $this->countrySource->toOptionArray();
    }
}
