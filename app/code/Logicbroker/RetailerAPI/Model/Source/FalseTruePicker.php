<?php
namespace Logicbroker\RetailerAPI\Model\Source;

class FalseTruePicker implements \Magento\Framework\Option\ArrayInterface
{
/**
  * {@inheritdoc}
  *
  * @codeCoverageIgnore
  */
    public function toOptionArray()
    {
        return [
            ['value' => 'false', 'label' => __('No')],
            ['value' => 'true', 'label' => __('Yes')],
        ];
    }
}
