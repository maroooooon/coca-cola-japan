<?php

namespace Coke\Logicbroker\Cron;

use CokeEurope\Catalog\Api\Data\ModerationStatusInterface;
use CokeEurope\Catalog\Model\Data\ModerationStatus;
use CokeEurope\PersonalizedProduct\Model\EnableRejectionCodesModelFactory;
use CokeEurope\PersonalizedProduct\Model\ResourceModel\EnableRejectionCodesModel\EnableRejectionCodesCollection;
use CokeEurope\PersonalizedProduct\Helper\Config as PersonalizedProductConfigHelper;
use CokeEurope\PersonalizedProduct\Helper\EmailHelper;
use Logicbroker\RetailerAPI\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Spi\OrderItemResourceInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Sales\Model\Service\CreditmemoService;

class LogicbrokerPullCancellations
{
	const CANCELLED_DOCUMENT_TYPE_ID = 29; /** TypeId 29 seems to be order cancelled on logicbroker */
	protected $helper;
	protected $repo;
	protected $apiUrl;
	protected $apiKey;
	protected $commentFactory;
	protected $searchCriteriaBuilder;
	private CreditmemoFactory $creditMemoFactory;
	private CreditmemoService $creditMemoService;
	private PersonalizedProductConfigHelper $personalizedProductConfigHelper;
	private EmailHelper $emailHelper;
	private OrderItemResourceInterface $orderItemResource;
	private EnableRejectionCodesModelFactory $enableRejectionCodesModelFactory;
	private EnableRejectionCodesCollection $enableRejectionCodesCollection;

	public function __construct(
		Data                            $helper,
		OrderRepositoryInterface        $orderRepository,
		HistoryFactory                  $commentFactory,
		SearchCriteriaBuilder           $searchCriteriaBuilder,
		CreditmemoFactory               $creditMemoFactory,
		CreditmemoService               $creditMemoService,
		PersonalizedProductConfigHelper $personalizedProductConfigHelper,
		EmailHelper                     $emailHelper,
		OrderItemResourceInterface      $orderItemResource,
		EnableRejectionCodesModelFactory $enableRejectionCodesModelFactory,
		EnableRejectionCodesCollection $enableRejectionCodesCollection
	) {
		$this->helper = $helper;
		$this->repo = $orderRepository;
		$this->commentFactory = $commentFactory;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->creditMemoFactory = $creditMemoFactory;
		$this->creditMemoService = $creditMemoService;
		$this->personalizedProductConfigHelper = $personalizedProductConfigHelper;
		$this->emailHelper = $emailHelper;
		$this->orderItemResource = $orderItemResource;
		$this->enableRejectionCodesModelFactory = $enableRejectionCodesModelFactory;
		$this->enableRejectionCodesCollection = $enableRejectionCodesCollection;
	}


	/**
	 * This function will get a list of cancelled orders from the API, then it will get a list of orders from Magento that
	 * match the increment Ids from the API, then it will get a list of rejection reasons from the database, then it will loop
	 * through the orders and update the order status to cancelled, add a comment to the order, set the moderation status of
	 * the order item to rejected, create a credit memo and then refund it, and then cancel the order
	 */
	public function execute()
	{
		/** Checking if there are any cancelled orders to pull. If there are none, it will log a message and return. */
		if (!$cancelledOrders = $this->getCancelledOrders()) {
			$this->helper->logInfo("No cancelled orders to pull.");
			return;
		}

		/** Gets a list of orders based on the increment Id provided from the api's cancelled orders.
		 * Filters the orders by a status not equal to closed or cancelled */
		$criteria = $this->searchCriteriaBuilder;
		$criteria->setFilterGroups(array());
		$criteria->addFilter("increment_id", array_column($cancelledOrders, 'OrderNumber'), "in");
		$criteria->addFilter("status", [Order::STATE_CLOSED, Order::STATE_CANCELED], "nin");
		$orderList = $this->repo->getList($criteria->create())->getItems();

		/** Getting the rejection reasons from the database. */
		$rejections = [];
		foreach ($this->enableRejectionCodesCollection->toArray()['items'] as $rejectionItem) {
			$rejections[$rejectionItem['code']] = $rejectionItem;
		}

		/** A foreach loop that is looping through the orderList array. */
		/** @var Order $order */
		foreach ($orderList as $order) {
			$logicBrokerKey = $cancelledOrders[$order->getIncrementId()]->LogicbrokerKey;

			try {
				if ($this->personalizedProductConfigHelper->getWebsiteModerationIsEnabled($order->getStore()->getWebsiteId())) {
					/** Getting the order events from the API. */
					$rejection = $this->getRejectionReason($logicBrokerKey, $rejections);
					/** Updating the order status to 'cancelled' and adding a comment to the order. */
					$this->addCommentToOrder($order, $logicBrokerKey);
					/** Setting the moderation status of the order item to rejected and saving it. */
					$this->rejectPendingItemsAndSendEmail($order, $rejection);
					/** Create a credit memo and then refund it. */
					$this->issueCreditMemoOfflineRefund($order);
					/** Cancel the order. */
					$this->cancelOrder($order);
				}
			} catch (\Exception $e) {
				$this->helper->logError('Error updating order with Logicbroker key:  '.$logicBrokerKey.': ' . $e->getMessage());
			}
		}
	}

	/**
	 * It gets a list of cancelled orders from the API
	 * @return array An array of orders that have been cancelled.
	 */
	public function getCancelledOrders(): array
	{
		/** Getting the API key from the helper class. If the API key is null, it will log a message and return. */
		$this->apiKey = $this->helper->getApiKey();
		if ($this->apiKey == null) {
			$this->helper->logInfo("API key is null, unable to pull cancellations.");
			return [];
		}

		$this->apiUrl = $this->helper->getApiUrl();
		$orders = [];

		try {
			$fromDate = new \DateTime('1 hour ago');
			$toDate = new \DateTime();
			$url = sprintf("%sapi/v1/Orders?filters.status=1100&filters.from=%s&filters.to=%s",
				$this->apiUrl, $fromDate->format('Y-m-d\TH:i'), $toDate->format('Y-m-d\TH:i'));
			$apiRes = $this->helper->getFromApi($url, array("Body","SalesOrders"));
			foreach ($apiRes['Result'] as $partial) {
				$orders[$partial->OrderNumber]= $partial;
			}
		} catch (\Exception $e) {
			$this->helper->logError('Error getting cancelled orders list from API: ' . $e->getMessage());
		}
		return $orders;
	}



	/**
	 * It returns the rejection reason and explanation for the order event.
	 * @param string $logicBrokerKey This is the key that you use to identify the order in LogicBroker.
	 * @param array $rejections An array of rejection reasons.
	 *
	 * @return array An array with the reason and explanation for the rejection.
	 */
	public function getRejectionReason(string $logicBrokerKey, array $rejections): array
	{
		$orderEventKey = $this->getOrderEvent($logicBrokerKey);
		if (!$orderEventKey) {
			return [];
		}

		$rejection = $rejections[$orderEventKey];
		if ($rejection) {
			return [
				'reason' => $rejection['short_description'],
				'explanation' => $rejection['long_description']
			];
		}

		return [];
	}

	/**
	 * It gets the order event from the API and returns an array of the cancelled order events
	 * @param string $logicBrokerKey The unique identifier for the order in Logicbroker.
	 * @return int|null
	 */
	public function getOrderEvent(string $logicBrokerKey): ?int
	{
		$event = null;
		try {
			$url = sprintf("%sapi/v1/Orders/%s/Events", $this->apiUrl, $logicBrokerKey);
			$apiRes = $this->helper->getFromApi($url, array("Body", "ActivityEvents"));

			/** Getting the order events from the API and returning an array of the cancelled order events. */
			foreach ($apiRes['Result'] as $partial) {
				if ($partial->TypeId === self::CANCELLED_DOCUMENT_TYPE_ID) {
					/** Getting the rejection reason from the API.*/
					$line =  json_decode($partial->AdditionalData)->Lines[0]->ChangeReason;
					$event = substr($line, 0, 3);
					(int)$event == 0 ? $event = null : $event = (int)$event;
				}
			}
		} catch (\Exception $e) {
			$this->helper->logError('Error getting cancelled orders events from API: ' . $e->getMessage());
		}

		return $event;
	}

	/**
	 * This function adds a comment to the order
	 *
	 * @param Order $order The order object
	 * @param string $lbKey The Logicbroker key for the order
	 */
	public function addCommentToOrder(Order $order, string $lbKey): void
	{
		try {
			$comment = "Received order cancellation (Logicbroker ID " . $lbKey. ").";
			$commentEntity = $this->commentFactory->create();
			$commentEntity->setOrder($order);
			$commentEntity->setComment($comment);
			$commentEntity->save();
		} catch (\Exception $e) {
			$this->helper->logError('Error adding comment to order: ' . $e->getMessage());
		}
	}

	/**
	 * It loops through all the items in the order, and if the item is pending moderation, it sets the moderation status to
	 * rejected and saves the item
	 *
	 * @param Order $order The order object
	 * @param array $rejection array of rejection reasons
	 */
	public function rejectPendingItemsAndSendEmail(Order $order, array $rejection): void
	{
		foreach ($order->getItems() as $orderItem) {
			if ($orderItem->getModerationStatus() == ModerationStatusInterface::MODERATION_STATUS_PENDING) {
				try {
					$orderItem->setModerationStatus(ModerationStatusInterface::MODERATION_STATUS_REJECTED);
					$this->orderItemResource->save($orderItem);
                    $order->getItemById($orderItem->getItemId())->setData('moderation_status', ModerationStatus::MODERATION_STATUS_REJECTED);
				} catch (\Exception $e) {
					$this->helper->logError('Error saving order item rejected status: ' . $e->getMessage());
				}
			}
		}
		$this->emailHelper->sendEmail(EmailHelper::REJECTED_MESSAGE, $order, $rejection);
	}

	/**
	 * It creates a credit memo and then refunds it offline.
	 * @param Order $order The order object
	 */
	public function issueCreditMemoOfflineRefund(Order $order): void
	{
		try {
			$creditMemo = $this->creditMemoFactory->createByOrder($order);
			$this->creditMemoService->refund($creditMemo);
			$this->creditMemoService->notify($creditMemo->getId());
		} catch (\Exception $e) {
			$this->helper->logError(sprintf("Refund could not be processed. Order #%s", $order->getIncrementId()));
		}
	}

	/**
	 * Cancel order.
	 * @param Order $order The order object
	 */
	public function cancelOrder(Order $order): void
	{
		try {
			$order->cancel();
		} catch(\Exception $e) {
			$this->helper->logError('Error cancelling the order ' . $order->getIncrementId() . ' : ' . $e->getMessage());
		}
	}
}
