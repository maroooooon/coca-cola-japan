<?php

namespace Coke\Whitelist\Observer;

use Coke\WhitelistEmail\Model\Email\Sender\OrderUnderReviewSender;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Coke\Whitelist\Api\Data\WhitelistInterface;
use Coke\Whitelist\Api\Data\WhitelistInterfaceFactory;
use Coke\Whitelist\Api\WhitelistOrderRepositoryInterface;
use Coke\Whitelist\Api\Data\WhitelistOrderInterfaceFactory;
use Coke\Whitelist\Api\Data\WhitelistOrderInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Psr\Log\LoggerInterface;
use Coke\Whitelist\Model\ModuleConfig;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var WhitelistRepositoryInterface
     */
    private $whitelistRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var WhitelistInterfaceFactory
     */
    private $whitelistFactory;

    /**
     * @var WhitelistOrderInterfaceFactory
     */
    private $whitelistOrderFactory;

    /**
     * @var WhitelistOrderRepositoryInterface
     */
    private $whitelistOrderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $config;
    /**
     * @var OrderUnderReviewSender
     */
    private $orderUnderReviewSender;

    /**
     * CheckoutSubmitAllAfter constructor.
     *
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WhitelistInterfaceFactory $whitelistFactory
     * @param WhitelistOrderRepositoryInterface $whitelistOrderRepository
     * @param WhitelistOrderInterfaceFactory $whitelistOrderFactory
     * @param LoggerInterface $logger
     * @param ModuleConfig $config
     * @param OrderUnderReviewSender $orderUnderReviewSender
     */
    public function __construct(
        WhitelistRepositoryInterface $whitelistRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WhitelistInterfaceFactory $whitelistFactory,
        WhitelistOrderRepositoryInterface $whitelistOrderRepository,
        WhitelistOrderInterfaceFactory $whitelistOrderFactory,
        LoggerInterface $logger,
        ModuleConfig $config,
        OrderUnderReviewSender $orderUnderReviewSender
    ) {
        $this->whitelistRepository = $whitelistRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->whitelistFactory = $whitelistFactory;
        $this->whitelistOrderFactory = $whitelistOrderFactory;
        $this->whitelistOrderRepository = $whitelistOrderRepository;
        $this->logger = $logger;
        $this->config = $config;
        $this->orderUnderReviewSender = $orderUnderReviewSender;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $deniedValues  = [];
        $pendingValues = [];

        foreach ($order->getItems() as $orderItem) {
            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();
            $options = $product->getOptions();
            $selectedOptions = $orderItem->getProductOptionByCode('options');

            if (!$selectedOptions) {
                continue;
            }

            foreach ($selectedOptions as $selectedOption) {
                $option = $this->findOption($options, $selectedOption);

                if ($option && $option->getWhitelistTypeId() && $option->getRequireNonWhitelistedValueApproval()) {
                    $whitelistItem = $this->getWhitelistItem($option->getWhitelistTypeId(), $selectedOption['value'], $order->getStoreId());

                    if (!$whitelistItem) {
                        $whitelistItem = $this->createWhitelistEntry($option->getWhitelistTypeId(), $selectedOption['value'], $order->getStoreId(), WhitelistStatus::PENDING);
                        if ($whitelistItem) {
                            $this->createWhitelistOrderEntry($order->getId(), $whitelistItem->getId());
                            $pendingValues[] = $selectedOption['value'];
                        }
                    } else if ($whitelistItem->getStatus() == WhitelistStatus::PENDING) {
                        $this->createWhitelistOrderEntry($order->getId(), $whitelistItem->getId());
                        $pendingValues[] = $selectedOption['value'];
                    } else if ($whitelistItem->getStatus() == WhitelistStatus::DENIED) {
                        $deniedValues[] = $selectedOption['value'];
                    }
                }
            }
        }

        if (!empty($deniedValues)) {
            $order->addCommentToStatusHistory(__('This order contained denied text: %1', implode(', ', $deniedValues)), 'denied');
        } else if (!empty($pendingValues)) {
            $order->addCommentToStatusHistory(__('This order contained pending text: %1', implode(', ', $pendingValues)), $this->config->getPendingWhitelistItemOrderStatus());
            $this->orderUnderReviewSender->send($order);
        }

        if (!empty($deniedValues) || !empty($pendingValues)) {
            $order->save();
        }
    }

    private function createWhitelistEntry($whitelistTypeId, $value, $storeId, $status)
    {
        /** @var WhitelistInterface $whitelistModel */
        $whitelistModel = $this->whitelistFactory->create();
        $whitelistModel->setTypeId($whitelistTypeId);
        $whitelistModel->setValue(htmlspecialchars_decode($value));
        $whitelistModel->setStatus($status);
        $whitelistModel->setStoreId($storeId);

        try {
            $this->whitelistRepository->save($whitelistModel);
        } catch (\Exception $e) {
            $this->logger->error($e);
            return false;
        }

        return $whitelistModel;
    }

    private function createWhitelistOrderEntry($orderId, $whitelistId)
    {
        /** @var WhitelistOrderInterface $whitelistOrderModel */
        $whitelistOrderModel = $this->whitelistOrderFactory->create();
        $whitelistOrderModel->setOrderId($orderId);
        $whitelistOrderModel->setWhitelistId($whitelistId);

        try {
            $this->whitelistOrderRepository->save($whitelistOrderModel);
        } catch (AlreadyExistsException $e) {
            // Do nothing, if the record exists, we're gucci
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }

    private function findOption($options, $selectedOption)
    {
        foreach ($options as $option) {
            if ($option->getId() === $selectedOption['option_id']) {
                return $option;
            }
        }

        return false;
    }

    private function getWhitelistItem($typeId, $value, $storeId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(WhitelistInterface::STORE_ID, $storeId)
            ->addFilter(WhitelistInterface::TYPE_ID, $typeId)
            ->addFilter(WhitelistInterface::VALUE, $value);

        $results = $this->whitelistRepository->getList($searchCriteria->create());

        if ($results->getTotalCount() === 1) {
            return array_values($results->getItems())[0];
        }

        return false;
    }
}
