<?php
/**
 * ApproveOrdersPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\PersonalizedProduct\Plugin;

use CokeEurope\Catalog\Api\Data\ModerationStatusInterface;
use CokeEurope\Catalog\Model\Data\ModerationStatus;
use CokeEurope\PersonalizedProduct\Helper\Config;
use CokeEurope\PersonalizedProduct\Helper\EmailHelper;
use Logicbroker\RetailerAPI\Helper\Data;
use Logicbroker\RetailerAPI\Jobs\Cron\PullShipments;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\ItemFactory;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Spi\OrderItemResourceInterface;
use Magento\Shipping\Model\ShipmentNotifier;

class ApproveOrdersPlugin extends PullShipments
{
	protected $helper;
	protected $apiUrl;
	protected $apiKey;
	private Config $config;
	private OrderItemInterfaceFactory $orderItemInterfaceFactory;
	private OrderItemResourceInterface $orderItemResource;
	private OrderRepositoryInterface $orderRepository;
	private SearchCriteriaBuilder $searchCriteriaBuilder;
	private ShipmentNotifier $shipmentNotifier;
	private EmailHelper $emailHelper;

	/**
	 * @param Data $helper
	 * @param OrderRepositoryInterface $orderRepository
	 * @param ShipmentRepositoryInterface $shipRepo
	 * @param ItemFactory $itemFactory
	 * @param TrackFactory $trackFactory
	 * @param ShipmentTrackRepositoryInterface $trackRepo
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param ShipmentNotifier $shipmentNotifier
	 * @param InvoiceService $invoiceService
	 * @param TransactionFactory $transactionFactory
	 * @param InvoiceSender $invoiceSender
	 * @param Config $config
	 * @param OrderItemInterfaceFactory $orderItemInterfaceFactory
	 * @param OrderItemResourceInterface $orderItemResource
	 * @param EmailHelper $emailHelper
	 */
	public function __construct(
		Data $helper,
		OrderRepositoryInterface $orderRepository,
		ShipmentRepositoryInterface $shipRepo,
		ItemFactory $itemFactory,
		TrackFactory $trackFactory,
		ShipmentTrackRepositoryInterface $trackRepo,
		SearchCriteriaBuilder $searchCriteriaBuilder,
		ShipmentNotifier $shipmentNotifier,
		InvoiceService $invoiceService,
		TransactionFactory $transactionFactory,
		InvoiceSender $invoiceSender,
		Config $config,
		OrderItemInterfaceFactory $orderItemInterfaceFactory,
		OrderItemResourceInterface $orderItemResource,
		EmailHelper $emailHelper
	)
	{
		$this->helper = $helper;
		$this->config = $config;
		$this->orderItemInterfaceFactory = $orderItemInterfaceFactory;
		$this->orderItemResource = $orderItemResource;
		$this->orderRepository = $orderRepository;
		$this->repo = $orderRepository;
		$this->shipRepo = $shipRepo;
		$this->itemFactory = $itemFactory;
		$this->trackFactory = $trackFactory;
		$this->trackRepo = $trackRepo;
		$this->search = $searchCriteriaBuilder;
		$this->notifier = $shipmentNotifier;
		$this->invoiceService = $invoiceService;
		$this->transactionFactory = $transactionFactory;
		$this->invoiceSender = $invoiceSender;
		$this->emailHelper = $emailHelper;
	}


	/**
	 * It gets all the shipments from the API, then for each shipment, it checks if the order has any pending items. If it
	 * does, it approves them and sends an email to the customer
	 * @param PullShipments $subject The class that called the plugin.
	 * @return array An empty array.
	 */
	public function beforeExecute(PullShipments $subject): array
	{
		$this->apiKey = $this->helper->getApiKey();
		if ($this->apiKey == null) {
			$this->helper->logInfo("API key is null, unable to pull shipments.");
			return [];
		}
		$this->apiUrl = $this->helper->getApiUrl();

		$shipments = $this->getShipments();
		/** @var Shipment $ship */
		foreach ($shipments as $ship)
		{
			$hadPendingItems = false;
			if (property_exists($ship, 'ExtendedAttributes')) {
				$orderId = $this->helper->getKeyValue($ship->ExtendedAttributes, 'SalesOrderNumber');
				if ($orderId == null) {
					continue;
				}

				/** Getting the order from the order repository. */
				$criteria = $this->search;
				$criteria->setFilterGroups(array());
				$criteria->addFilter("increment_id", $orderId, "eq");
				$orderList = $this->repo->getList($criteria->create())->getItems();

				/** @var Order $order */
				$order = reset($orderList);

				/** Checking if the order exists, if it does not, it skips the order. */
				if (count($orderList) == 0 || $order == null || !$this->config->getStoreModerationIsEnabled($order->getStore()->getId())) {
					continue;
				}

				/** Iterating through the shipment lines and checking if the order item has a moderation status of pending. If it does,
				it approves it. */
				foreach ($ship->ShipmentLines as $shipItem) {
					$item_id = $this->helper->getKeyValue($shipItem->ExtendedAttributes, "item_id");
					/** @var OrderItemInterface $orderItem */
					$item = $this->orderItemInterfaceFactory->create();
					$this->orderItemResource->load($item, $item_id, 'item_id');

					if ((int) $item->getModerationStatus() === ModerationStatusInterface::MODERATION_STATUS_PENDING) {
						$this->approvePendingItem($item, $order);
                        $order->getItemById($item->getItemId())->setData('moderation_status', ModerationStatus::MODERATION_STATUS_APPROVED);
						$hadPendingItems = true;
					}
				}

				/** Sending an email to the customer to let them know that their order has been approved. */
				if ($hadPendingItems) {
					$this->emailHelper->sendEmail(EmailHelper::APPROVED_MESSAGE, $order);
				}
			}
		}

		return [];
	}


	/**
	 * It sets the moderation status of the order item to approved and saves it
	 * @param OrderItemInterface $item The order item that is being moderated.
	 */
	public function approvePendingItem(OrderItemInterface $item, Order $order): void
	{
		try {
			$item->setModerationStatus(ModerationStatusInterface::MODERATION_STATUS_APPROVED);
			$this->orderItemResource->save($item);
		} catch (\Exception $e) {
			$this->helper->logError('Error saving order item approved status: ' . $e->getMessage());
		}
	}
}
