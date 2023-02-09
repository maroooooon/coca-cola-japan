<?php
namespace Logicbroker\RetailerAPI\Model\Source;

class ApiAddress implements \Magento\Framework\Option\ArrayInterface
{
/**
  * {@inheritdoc}
  *
  * @codeCoverageIgnore
  */
    public function toOptionArray()
    {
        return [
            ['value' => 'https://commerceapi.io/', 'label' => __('Production')],
            ['value' => 'https://stage.commerceapi.io/', 'label' => __('Staging')],
        ];
    }
}
