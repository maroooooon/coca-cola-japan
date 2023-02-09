<?php

namespace Coke\Japan\Plugin;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Coke\Japan\Model\Website;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Sarp2ProfileInterfacePlugin
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
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    /**
     * @param ProfileInterface $subject
     * @param $result
     * @return Phrase|mixed
     */
    public function afterGetCustomerFullname(ProfileInterface $subject, $result)
    {
        try {
            if (!($store = $this->storeManager->getStore($subject->getStoreId()))
                || $store->getWebsite()->getCode() !== Website::MARCHE) {
                return $result;
            }

            return __('%1 %2', $subject->getCustomerLastname(), $subject->getCustomerFirstname());
        } catch (NoSuchEntityException $e) {
            $this->logger->info(__('[Sarp2ProfileInterfacePlugin] %1', $e->getMessage()));
        }

        return $result;
    }
}
