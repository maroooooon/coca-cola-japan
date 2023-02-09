<?php

namespace Coke\CancelOrder\Observer;

use Coke\CancelOrder\Helper\CancelOrderHelper;
use Coke\CancelOrder\Helper\Config;
use Coke\CancelOrder\Logger\Logger;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var CancelOrderHelper
     */
    private $cancelOrderHelper;

    /**
     * @param Logger $logger
     * @param Config $config
     * @param OrderRepositoryInterface $orderRepository
     * @param CancelOrderHelper $cancelOrderHelper
     */
    public function __construct(
        Logger $logger,
        Config $config,
        OrderRepositoryInterface $orderRepository,
        CancelOrderHelper $cancelOrderHelper
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->cancelOrderHelper = $cancelOrderHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');

        if ($this->config->isEnabled($order->getStoreId()) && !($order->hasInvoices())) {
            $orderStatus = $this->config->getOrderStatus($order->getStoreId());
            $orderState = $this->cancelOrderHelper->getStateFromStatus($orderStatus);
            $order->setState($orderState);
            $order->setStatus($orderStatus);
            $this->orderRepository->save($order);
        }

        $data = [
            'order_id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'state' => $order->getState(),
            'status' => $order->getStatus()
        ];
        $this->logger->info(__('[SalesOrderPlaceAfterObserver] Order data: %1', print_r($data, true)));
    }
}
