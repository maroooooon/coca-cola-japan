<?php

namespace Coke\CancelOrder\Cron;

use Coke\CancelOrder\Logger\Logger;
use Coke\CancelOrder\Model\Aggregator\PendingOrdersAggregator;
use Magento\Framework\App\Area;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Store\Model\App\Emulation;
use StripeIntegration\Payments\Model\Config;

class ProcessPendingOrders
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var PendingOrdersAggregator
     */
    private $pendingOrderAggregator;
    /**
     * @var InvoiceSender
     */
    private $invoiceSender;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var SalesData
     */
    private $salesData;
    /**
     * @var TransactionFactory
     */
    private $transactionFactory;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Config
     */
    private $stripeConfig;

    /**
     * @param Logger $logger
     * @param PendingOrdersAggregator $pendingOrderAggregator
     * @param InvoiceSender $invoiceSender
     * @param InvoiceService $invoiceService
     * @param SalesData $salesData
     * @param TransactionFactory $transactionFactory
     * @param Emulation $emulation
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Logger $logger,
        PendingOrdersAggregator $pendingOrderAggregator,
        InvoiceSender $invoiceSender,
        InvoiceService $invoiceService,
        SalesData $salesData,
        TransactionFactory $transactionFactory,
        Emulation $emulation,
        OrderRepositoryInterface $orderRepository,
        Config $stripeConfig
    ) {
        $this->logger = $logger;
        $this->pendingOrderAggregator = $pendingOrderAggregator;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->salesData = $salesData;
        $this->transactionFactory = $transactionFactory;
        $this->emulation = $emulation;
        $this->orderRepository = $orderRepository;
        $this->stripeConfig = $stripeConfig;
    }

    public function resetStripeConfiguration(OrderInterface $order)
    {
        $paymentMethodCode = $order->getPayment()->getMethod();
        if (strpos($paymentMethodCode, "stripe") === -1) {
            // ignore because this order was not placed with stripe.
            return;
        }

        $this->stripeConfig->reInitStripe($order->getStoreId(), $order->getOrderCurrencyCode(), null);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $successfulOrders = [];
        $failedOrders = [];
        $enabledStoreIds = $this->pendingOrderAggregator->getEnabledStoreIds();

        foreach ($enabledStoreIds as $storeId) {
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

            foreach ($this->pendingOrderAggregator->getOrders($storeId) as $order) {
                try {
                    $this->resetStripeConfiguration($order);

                    $this->invoiceOrder($order);
                    $successfulOrders[] = $order->getEntityId();
                } catch (LocalizedException | \Exception $exception) {
                    $this->logger->info(
                        __(
                            '[ProcessPendingOrders] Unable to invoice Order ID %1 (Increment ID: %2). Error: %3',
                            $order->getEntityId(), $order->getIncrementId(), $exception->getMessage()
                        )
                    );
                    $order->addCommentToStatusHistory(
                        __('Error processing order on the cron. Error message: %1', $exception->getMessage())
                    );
                    $this->orderRepository->save($order);
                    $failedOrders[] = $order->getEntityId();
                }
            }

            $this->emulation->stopEnvironmentEmulation();
        }

        $this->logInvoicedOrders($successfulOrders, $failedOrders);
    }

    /**
     * @param OrderInterface $order
     * @throws LocalizedException
     * @throws \Exception
     */
    private function invoiceOrder(OrderInterface $order)
    {
        if (!$order->canInvoice()) {
            throw new LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }
        $invoice = $this->invoiceService->prepareInvoice($order);

        if (!$invoice) {
            throw new LocalizedException(__("The invoice can't be saved at this time. Please try again later."));
        }

        if (!$invoice->getTotalQty()) {
            throw new LocalizedException(
                __("The invoice can't be created without products. Add products and try again.")
            );
        }

        //$captureCase = $order->getPayment()->getLastTransId() ? Invoice::CAPTURE_ONLINE : Invoice::CAPTURE_OFFLINE;
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);
        $order->addCommentToStatusHistory('Order invoiced automatically via the cron.');
        $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
        $transactionSave->save();

        if ($this->salesData->canSendNewInvoiceEmail($order->getStoreId())) {
            $this->invoiceSender->send($invoice);
        }
    }

    /**
     * @param $successfulOrders
     * @param $failedOrders
     */
    private function logInvoicedOrders($successfulOrders, $failedOrders)
    {
        if (count($successfulOrders)) {
            $this->logger->info(
                __(
                    '[ProcessPendingOrders] Successfully invoiced Order IDs: %1',
                    implode(', ', $successfulOrders)
                )
            );
        }

        if (count($failedOrders)) {
            $this->logger->info(
                __(
                    '[ProcessPendingOrders] Unable to invoice Order IDs: %1',
                    implode(', ', $failedOrders)
                )
            );
        }
    }
}
