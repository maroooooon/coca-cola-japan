<?php

namespace Coke\Whitelist\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Indexer\Model\Indexer\CollectionFactory;
use Magento\Indexer\Model\IndexerFactory;
use Psr\Log\LoggerInterface;
use Throwable;

class ReindexAfterWhitelistSave implements ObserverInterface
{
    private static $indexerIds = ['catalog_product_attribute'];

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var IndexerFactory
     */
    private $indexerFactory;
    /**
     * @var CollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * ReindexAndClearCacheAfterWhitelistSave constructor.
     * @param LoggerInterface $logger
     * @param IndexerFactory $indexerFactory
     * @param CollectionFactory $indexerCollectionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        IndexerFactory $indexerFactory,
        CollectionFactory $indexerCollectionFactory
    ) {
        $this->logger = $logger;
        $this->indexerFactory = $indexerFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
    }

    /**
     * @param Observer $observer
     * @throws Throwable
     */
    public function execute(Observer $observer)
    {
        /** @var \Coke\Whitelist\Model\Whitelist $whitelist */
        $whitelist = $observer->getEvent()->getDataObject();
        if (!$whitelist->hasDataChanges()) {
            return;
        }

        $this->reindex();
    }

    /**
     * @throws Throwable
     */
    private function reindex()
    {
        foreach (self::$indexerIds as $indexerId) {
            $indexer = $this->indexerFactory->create()->load($indexerId);
            $indexer->reindexAll();
        }
    }
}
