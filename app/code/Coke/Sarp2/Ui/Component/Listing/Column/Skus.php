<?php

namespace Coke\Sarp2\Ui\Component\Listing\Column;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use Psr\Log\LoggerInterface;

class Skus extends Column
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $skus = $this->getSkusByProfileId($item['profile_id']);
                $item[$this->getData('name')] = implode(',', $skus);
            }
        }

        return $dataSource;
    }

    /**
     * @param int $profileId
     * @return array|null
     */
    private function getSkusByProfileId(int $profileId): ?array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            'aw_sarp2_profile_item',
            'sku'
        )->where('profile_id = ?', $profileId);

        return $connection->fetchCol($select);
    }
}
