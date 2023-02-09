<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEgypt\Customer\Observer;

use CokeEgypt\Customer\Helper\Config;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\OrderRepositoryInterface;

class BeforePlaceOrder implements ObserverInterface
{

	private Config $config;
	private DateTime $dateTime;
	private OrderRepositoryInterface $orderRepository;
	private SearchCriteriaBuilder $searchCriteriaBuilder;

	public function __construct(
		Config                   $config,
		DateTime                 $dateTime,
		OrderRepositoryInterface $orderRepository,
		SearchCriteriaBuilder    $searchCriteriaBuilder
	)
	{
		$this->config = $config;
		$this->dateTime = $dateTime;
		$this->orderRepository = $orderRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
	}

	/**
	 * Execute observer that will prevent customers from placing the same order within 48 hours.
	 *
	 * @param Observer $observer
	 * @return void
	 * @throws PaymentException
	 */
	public function execute(
		Observer $observer
	)
	{

		// Skip observer if CokeEgypt_Customer module is not enabled for this store view
		if(!$this->config->isEnabled()) {
			return;
		}

		// Create an array for the current order to compare with recent orders
		$currentOrderItems = [];
		$order = $observer->getEvent()->getData('order');
		foreach ($order->getAllItems() as $item) {
			$currentOrderItems = [
				'sku' => $item->getSku(),
				'qty' => $item->getQtyOrdered()
			];
		}
		$currentOrder = [
			'items' => $currentOrderItems,
			'total' => $order->getGrandTotal(),
		];

		// Get all customer orders placed within the last two days and create arrays for comparison with current order
		$currentDate = $this->dateTime->date('Y-m-d H:i:s');
		$twoDaysAgo = $this->dateTime->date('Y-m-d H:i:s', strtotime($currentDate . " -2 days"));
		$this->searchCriteriaBuilder
			->addFilter('created_at', $twoDaysAgo, 'gteq')
			->addFilter('customer_id', $order->getCustomerId());
		$searchResults = $this->orderRepository->getList($this->searchCriteriaBuilder->create());
		$recentOrders = [];
		foreach($searchResults->getItems() as $recentOrder) {
			$recentOrderItems = [];
			foreach($recentOrder->getItems() as $item){
				$recentOrderItems = [
					'sku' => $item->getSku(),
					'qty' => $item->getQtyOrdered()
				];
			}
			$recentOrders[] = [
				'items' => $recentOrderItems,
				'total' => $recentOrder->getGrandTotal(),
			];
		}

		// Compare each recent order with the current order to see if any match
		$hasMatchingOrder = false;
		foreach($recentOrders as $recentOrder) {
			if(( $currentOrder == $recentOrder )) {
				$hasMatchingOrder = true;
			}
		}

		// Throw an exception if customer has a recent order that matches the current order
		if($hasMatchingOrder){
			$message = 'This order matches an order you have placed recently. Please wait 48 hours before placing the same order again.';
			throw new PaymentException(__($message));
		}
	}
}
