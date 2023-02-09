<?php

namespace FortyFour\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Brand extends AbstractRenderer
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var Quote
     */
    private $quoteSession;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * Brand constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        ResourceConnection $resourceConnection,
        Quote $quoteSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->resourceConnection = $resourceConnection;
        $this->quoteSession = $quoteSession;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if (isset($row['brand'])) {
            $row['brand'] = __('%1', $this->getAttributeValueByOptionId($row['brand']));
        }
        return parent::render($row);
    }

    /**
     * @param $valueId
     * @return string|null
     */
    private function getAttributeValueByOptionId($valueId): ?string
    {
        if (!$valueId) {
            return '';
        }

        $storeId = $this->quoteSession->getStoreId();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('eav_attribute_option_value'),
            'value'
        )->where(
            'option_id = ?', $valueId
        )->where(
            'store_id = ?', $storeId
        );

        return $connection->fetchOne($select) ?? null;
    }
    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }
}
