<?php

namespace Coke\OrderGrid\Model\ResourceModel\Order\Grid;

use Magento\Framework\DB\Select;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    /**
     * Hook for operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        if ($select = $this->getSelect()) {
            $salesOrderItemJoinTable = $this->getTable('sales_order_item');

            $select->reset(Select::ORDER);

            $select->join(
                $salesOrderItemJoinTable . ' as soi',
                'main_table.entity_id = soi.order_id',
                ['sku' => 'GROUP_CONCAT(soi.sku SEPARATOR ", ")']
            );

            $select->group('main_table.entity_id');
        }

        parent::_renderFiltersBefore();
    }
}
