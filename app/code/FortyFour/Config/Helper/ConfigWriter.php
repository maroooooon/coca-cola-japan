<?php

namespace FortyFour\Config\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

use Magento\Framework\App\Helper\AbstractHelper;

class ConfigWriter extends AbstractHelper
{
    /**
     * @var WriterInterface
     */
    public $configWriter;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ConfigWriter constructor.
     * @param Context $context
     * @param WriterInterface $configWriter
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->configWriter = $configWriter;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getStoreIdByCode(string $code): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store'),
            'store_id'
        )->where("code = ?", $code);

        return $connection->fetchOne($select);
    }

    /**
     * @param string $code
     * @return string
     */
    public function getWebsiteIdByCode(string $code): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store_website'),
            'website_id'
        )->where("code = ?", $code);

        return $connection->fetchOne($select);
    }

    /**
     * @return WriterInterface
     */
    public function getConfigWriter(): WriterInterface
    {
        return $this->configWriter;
    }
}
