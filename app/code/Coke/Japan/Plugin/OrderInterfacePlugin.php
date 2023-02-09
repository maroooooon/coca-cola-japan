<?php

namespace Coke\Japan\Plugin;

use Coke\Japan\Model\Website;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class OrderInterfacePlugin
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
     * @param OrderInterface $subject
     * @param $result
     * @return OrderInterface|mixed
     */
    public function afterGetCustomerName(OrderInterface $subject, $result)
    {
        try {
            if ($subject->getStore()->getWebsite()->getCode() !== Website::MARCHE) {
                return $result;
            }

            return __('%1 %2', $subject->getCustomerLastname(), $subject->getCustomerFirstname());
        } catch (NoSuchEntityException $e) {
            $this->logger->info(__('[OrderInterfacePlugin] %1', $e->getMessage()));
        }

        return $result;
    }
}
