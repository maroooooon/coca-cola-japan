<?php
namespace Logicbroker\RetailerAPI\Model\Source;

class TrueFalsePicker implements \Magento\Framework\Option\ArrayInterface
{
/**
  * {@inheritdoc}
  *
  * @codeCoverageIgnore
  */
    public function toOptionArray()
    {
        return [
            ['value' => 'true', 'label' => __('Yes')],
            ['value' => 'false', 'label' => __('No')]
        ];
    }
}
