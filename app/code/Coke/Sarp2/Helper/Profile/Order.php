<?php

namespace Coke\Sarp2\Helper\Profile;

use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Aheadworks\Sarp2\Model\ResourceModel\Profile\Order\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class Order
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CollectionFactory
     */
    private $profileOrderCollectionFactory;
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @param LoggerInterface $logger
     * @param CollectionFactory $profileOrderCollectionFactory
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CollectionFactory $profileOrderCollectionFactory,
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->logger = $logger;
        $this->profileOrderCollectionFactory = $profileOrderCollectionFactory;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param OrderInterface $order
     * @return \Aheadworks\Sarp2\Model\Profile\Order|null
     */
    public function getProfileIdFromOrder(OrderInterface $order): ?\Aheadworks\Sarp2\Model\Profile\Order
    {
        return $this->profileOrderCollectionFactory->create()
            ->addFieldToFilter('order_id', $order->getEntityId())
            ->addFieldToSelect('profile_id')->getFirstItem();
    }

    /**
     * @param OrderInterface $order
     * @return \Aheadworks\Sarp2\Api\Data\ProfileInterface|null
     */
    public function getProfileFromOrder(OrderInterface $order): ?\Aheadworks\Sarp2\Api\Data\ProfileInterface
    {
        if (!($profile = $this->getProfileIdFromOrder($order))) {
            return null;
        }

        try {
            return $this->profileRepository->get($profile->getData('profile_id'));
        } catch (LocalizedException $e) {
            return null;
        }
    }
}
