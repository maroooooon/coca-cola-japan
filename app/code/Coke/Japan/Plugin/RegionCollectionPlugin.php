<?php

namespace Coke\Japan\Plugin;

use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class RegionCollectionPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    /**
     * Remove region sort order
     *
     * @param Collection $subject
     * @param $result
     * @return Select
     */
    public function afterGetSelect(Collection $subject, $result)
    {
        /** @var Select $result */
        try {
            if ($this->storeManager->getStore()->getWebsite()->getCode() !== \Coke\Japan\Model\Website::MARCHE) {
                return $result;
            }

            $result->reset(Select::ORDER);
            return $result;
        } catch (NoSuchEntityException $e) {
            $this->logger->info(__('[RegionCollectionPlugin] %1', $e->getMessage()));
        }

        return $result;
    }
}
