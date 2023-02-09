<?php

namespace Coke\Whitelist\Plugin;

use Coke\Whitelist\Model\WhiteListHelper;
use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class OrderDataProviderPlugin
{
    /**
     * @var WhiteListHelper
     */
    private $whiteListHelper;

    /**
     * OrderDataProviderPlugin constructor.
     * @param WhiteListHelper $whiteListHelper
     */
    public function __construct(
        WhiteListHelper $whiteListHelper
    ) {
        $this->whiteListHelper = $whiteListHelper;
    }

    /**
     * @param CollectionFactory $subject
     * @param $result
     * @param $requestName
     * @return mixed
     */
    public function afterGetReport(
        CollectionFactory $subject,
        $result,
        $requestName
    ) {
        if ($requestName != 'sales_order_grid_data_source') {
            return $result;
        }

        /** @var Collection $result */
        foreach ($result->getItems() as $order) {
            if (($productOptions = $this->getProductOptions($result, $order->getEntityId()))
                && $whitelistItems = $this->whiteListHelper->getWhitelistValuesFromProductOptions($productOptions)) {
                $order->setData('whitelist_items', $whitelistItems);
            }
        }

        return $result;
    }

    /**
     * @param Collection $collection
     * @param int $orderId
     * @return array|null
     */
    private function getProductOptions(Collection $collection, int $orderId): ?array
    {
        $connection = $collection->getConnection();
        $query = $connection->select()->from(
            $connection->getTableName('sales_order_item'),
            'product_options'
        )->where(
            'order_id = ?', $orderId
        )->distinct(true);

        return $connection->fetchCol($query) ?: null;
    }
}
