<?php

namespace Coke\Logicbroker\Cron;

use Logicbroker\RetailerAPI\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Shipment\ItemFactory;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Shipping\Model\ShipmentNotifier;

class LogicbrokerPullShipmentsFakeable extends LogicbrokerPullShipments
{
    /**
     * @var array
     */
    private $shipmentFiles = [];

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
        ShipmentFactory $shipmentFactory,
        array $shipmentFiles = []
    )
    {
        parent::__construct(
            $helper, $orderRepository, $shipRepo, $itemFactory, $trackFactory, $trackRepo,
            $searchCriteriaBuilder, $shipmentNotifier, $invoiceService, $transactionFactory,
            $invoiceSender, $shipmentFactory
        );
        $this->shipmentFiles = $shipmentFiles;
    }

    public function getShipments()
    {
        $shipments = [];
        foreach ($this->shipmentFiles as $file) {
            $shipments[] = json_decode(file_get_contents($file));
        }
        return $shipments;
    }
}
