<?php

namespace FortyFour\SalesSequence\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveInvalidSequenceMeta extends Command
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * RemoveInvalidSequenceMeta constructor.
     * @param ResourceConnection $resourceConnection
     * @param string|null $name
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        string $name = null
    ) {
        parent::__construct($name);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('sales:sequence:remove-invalid-meta');
        $this->setDescription('Remove the invalid sales sequence meta table(s).');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Started removing invalid sales sequence meta...</info>');
        $storeIds = $this->getStoreIds();
        $invalidStoreIds = $this->getInvalidSalesSequenceStoreIds($storeIds);

        $output->writeln('<comment>Removing sales sequence meta for the following store ids: '
            . implode(', ', $invalidStoreIds) . '</comment>');

        $this->removeInvalidSalesSequenceMeta($invalidStoreIds);
        $output->writeln('<info>Finished removing invalid sales sequence meta.</info>');
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

    /**
     * @return array
     */
    private function getStoreIds()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store'),
            'store_id'
        )->distinct(true);

        return $connection->fetchCol($select);
    }

    /**
     * @param array $storeIds
     * @return array
     */
    private function getInvalidSalesSequenceStoreIds(array $storeIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('sales_sequence_meta'),
            'store_id'
        )->where(
            'store_id NOT IN (?)',
            $storeIds
        )->distinct(true);

        return $connection->fetchCol($select);
    }

    /**
     * @param array $invalidStoreIds
     * @return void
     */
    private function removeInvalidSalesSequenceMeta(array $invalidStoreIds): void
    {
        $connection = $this->getConnection();
        $connection->delete(
            $connection->getTableName('sales_sequence_meta'),
            ['store_id IN (?)' => $invalidStoreIds]
        );
    }
}
