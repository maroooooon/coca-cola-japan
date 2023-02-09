<?php

namespace Coke\Japan\Plugin;

use Aheadworks\Sarp2Stripe\Gateway\SubjectReader;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

class Sarp2TransactionRefundDataBuilderPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubjectReader
     */
    private $subjectReader;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param LoggerInterface $logger
     * @param SubjectReader $subjectReader
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        LoggerInterface $logger,
        SubjectReader $subjectReader,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->logger = $logger;
        $this->subjectReader = $subjectReader;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param \Aheadworks\Sarp2Stripe\Gateway\Request\TransactionRefundDataBuilder $subject
     * @param array $buildSubject
     * @return array[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeBuild(
        \Aheadworks\Sarp2Stripe\Gateway\Request\TransactionRefundDataBuilder $subject,
        array $buildSubject
    ) {
        $order = $this->subjectReader->readPayment($buildSubject)->getOrder();
        if (($store = $this->getStore($order->getStoreId()))
            && $store->getWebsite()->getCode() === \Coke\Japan\Model\Website::MARCHE) {

            if (isset($buildSubject['amount'])) {
                $buildSubject['amount'] /= 100;
            }
        }

        return [$buildSubject];
    }

    /**
     * @param int $storeId
     * @return \Magento\Store\Api\Data\StoreInterface|null
     */
    private function getStore(int $storeId): ?\Magento\Store\Api\Data\StoreInterface
    {
        try {
            return $this->storeRepository->getById($storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
