<?php

namespace Coke\Whitelist\Model\Service;

use Coke\Whitelist\Api\WhitelistManagementInterface;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Coke\Whitelist\Api\WhitelistOrderRepositoryInterface;
use Coke\Whitelist\Model\ModuleConfig;
use Coke\WhitelistEmail\Model\Email\Sender\OrderApprovedSender;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Coke\Whitelist\Api\Data\WhitelistOrderInterface;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Coke\Whitelist\Model\ResourceModel\WhitelistOrder\CollectionFactory as WhitelistOrderCollectionFactory;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

class Whitelist implements WhitelistManagementInterface
{
    /**
     * @var WhitelistRepositoryInterface
     */
    private $whitelistRepository;

    /**
     * @var WhitelistOrderRepositoryInterface
     */
    private $whitelistOrderRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var WhitelistOrderCollectionFactory
     */
    private $whitelistOrderCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ModuleConfig
     */
    private $config;
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * @var OrderApprovedSender
     */
    private $orderApprovedSender;
    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * Whitelist constructor.
     *
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param WhitelistOrderRepositoryInterface $whitelistOrderRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WhitelistOrderCollectionFactory $whitelistOrderCollectionFactory
     * @param LoggerInterface $logger
     * @param ModuleConfig $config
     * @param ManagerInterface $eventManager
     * @param OrderApprovedSender $orderApprovedSender
     * @param Emulation $emulation
     */
    public function __construct(
        WhitelistRepositoryInterface $whitelistRepository,
        WhitelistOrderRepositoryInterface $whitelistOrderRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WhitelistOrderCollectionFactory $whitelistOrderCollectionFactory,
        LoggerInterface $logger,
        ModuleConfig $config,
        ManagerInterface $eventManager,
        OrderApprovedSender $orderApprovedSender,
        Emulation $emulation
    ) {
        $this->whitelistRepository = $whitelistRepository;
        $this->whitelistOrderRepository = $whitelistOrderRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->whitelistOrderCollectionFactory = $whitelistOrderCollectionFactory;
        $this->logger = $logger;
        $this->config = $config;
        $this->eventManager = $eventManager;
        $this->orderApprovedSender = $orderApprovedSender;
        $this->emulation = $emulation;
    }

    /**
     * @inheritdoc
     */
    public function approve($id)
    {
        try {
            $whitelist = $this->whitelistRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e);
            return false;
        }

        $whitelist->setStatus(WhitelistStatus::APPROVED);

        try {
            $this->whitelistRepository->save($whitelist);
        } catch (\Exception $e) {
            $this->logger->error($e);
            return false;
        }

        return $this->updateOrdersForWhitelistForApproved($id);
    }

    /**
     * @inheritdoc
     */
    public function updateOrdersForWhitelistForApproved($whitelistId)
    {
        $whitelistOrders = $this->getWhitelistOrders($whitelistId);

        try {
            foreach ($whitelistOrders->getItems() as $whitelistOrder) {
                $order = $this->orderRepository->get($whitelistOrder->getOrderId());
                $orderStatus = $this->config->getApprovedWhitelistItemOrderStatus($order->getStoreId());
                $this->whitelistOrderRepository->delete($whitelistOrder);

                if ($this->canOrderBeSetToProcessing($order)) {
                    $order->addCommentToStatusHistory(__('All pending text approved.'), $orderStatus);
                    $this->orderRepository->save($order);
                    $this->emulation->startEnvironmentEmulation($order->getStoreId());
                    $this->orderApprovedSender->send($order);
                    $this->emulation->stopEnvironmentEmulation();
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateOrdersForWhitelistForDenied($whitelistId)
    {
        $whitelistOrders = $this->getWhitelistOrders($whitelistId);

        try {
            foreach ($whitelistOrders->getItems() as $whitelistOrder) {
                $order = $this->orderRepository->get($whitelistOrder->getOrderId());
                $orderStatus = $this->config->getDeniedWhitelistItemOrderStatus($order->getStoreId());
                $this->whitelistOrderRepository->delete($whitelistOrder);

                if ($order->getStatus() != 'denied') {
                    $order->addCommentToStatusHistory(__('Pending text was denied.'), $orderStatus);
                    $this->orderRepository->save($order);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deny($id)
    {
        try {
            $whitelist = $this->whitelistRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e);
            return false;
        }

        $whitelist->setStatus(WhitelistStatus::DENIED);

        try {
            $this->whitelistRepository->save($whitelist);
        } catch (\Exception $e) {
            $this->logger->error($e);
            return false;
        }

        return $this->updateOrdersForWhitelistForDenied($id);
    }

    private function getWhitelistOrders($whitelistId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(WhitelistOrderInterface::WHITELIST_ID, $whitelistId);

        return $this->whitelistOrderRepository->getList($searchCriteria->create());
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    private function canOrderBeSetToProcessing(OrderInterface $order)
    {
        $invalidStatuses = [
            $this->config->getApprovedWhitelistItemOrderStatus($order->getStoreId()),
            $this->config->getDeniedWhitelistItemOrderStatus($order->getStoreId()),
            \Magento\Sales\Model\Order::STATE_CANCELED
        ];

        if (in_array($order->getStatus(), $invalidStatuses)) {
            return false;
        }

        $collection = $this->whitelistOrderCollectionFactory->create();
        $collection->addFilter(WhitelistOrderInterface::ORDER_ID, $order->getEntityId());
        return $collection->getSize() == 0;
    }
}
