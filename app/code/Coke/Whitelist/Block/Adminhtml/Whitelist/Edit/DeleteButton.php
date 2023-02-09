<?php

namespace Coke\Whitelist\Block\Adminhtml\Whitelist\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getId()) {
            return [];
        }

        return [
            'label' => __('Delete Whitelist Item'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __(
                'Are you sure you want to delete this whitelisted item?'
            ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
            'sort_order' => 20,
        ];
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getId()]);
    }
}
