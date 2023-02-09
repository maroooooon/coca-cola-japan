<?php

namespace FortyFour\Voucher\Observer;

use FortyFour\Voucher\Helper\Voucher as VoucherHelper;
use FortyFour\Voucher\Model\Email\Sender\VouchersEmailSender;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SendVouchersToCustomer implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var VoucherHelper
     */
    private $voucherHelper;
    /**
     * @var VouchersEmailSender
     */
    private $vouchersEmailSender;

    /**
     * SendVouchersToCustomer constructor.
     * @param LoggerInterface $logger
     * @param VoucherHelper $voucherHelper
     * @param VouchersEmailSender $vouchersEmailSender
     */
    public function __construct(
        LoggerInterface $logger,
        VoucherHelper $voucherHelper,
        VouchersEmailSender $vouchersEmailSender
    ) {
        $this->logger = $logger;
        $this->voucherHelper = $voucherHelper;
        $this->vouchersEmailSender = $vouchersEmailSender;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->voucherHelper->isEnabled()) {
            return;
        }

        try {
            /* @var \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getData('order');

            if (!$order->getId()) {
                return;
            }

            $voucherSkus = $this->voucherHelper->getVoucherSkus();
            $voucherSkuQtyData = [];
            foreach ($voucherSkus as $voucherSku) {
                if (in_array(
                    $voucherSku,
                    $this->voucherHelper->getProductSkusFromOrderById($order->getId()))
                ) {
                    $voucherSkuQtyData[] = [
                        'sku' => $voucherSku,
                        'number_of_vouchers_to_send' => $this->voucherHelper->getNumberOfVouchersToSend(
                            $voucherSku,
                            $order->getId()
                        )
                    ];
                }
            }

            if (!$voucherSkuQtyData) {
                return;
            }

            foreach ($voucherSkuQtyData as $voucherSkuQtyDatum) {
                $vouchers = $this->voucherHelper->getVouchersToSendToCustomer(
                    $voucherSkuQtyDatum['number_of_vouchers_to_send']
                );
                if (!$vouchers) {
                    $this->logger->info(__('[SendVouchersToCustomer] %1', 'No vouchers to send.'));
                    continue;
                }

                if ($this->sendVouchersEmail($order, $voucherSkuQtyDatum['sku'], $vouchers)) {
                    $this->voucherHelper->setCouponsSentToCustomerById(array_keys($vouchers));
                }
            }
        } catch (\Exception $e) {
            $this->logger->info(__('[SendVouchersToCustomer] %1', $e->getMessage()));
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $voucherProductSku
     * @param array $vouchers
     * @return bool
     */
    private function sendVouchersEmail(
        \Magento\Sales\Model\Order $order,
        string $voucherProductSku,
        array $vouchers
    ): bool {
        try {
            $transport = [
                'order_data' => [
                    'customer_name' => __('%1 %2', $order->getCustomerFirstname(), $order->getCustomerLastname())
                ],
                'voucher_product' => $this->voucherHelper->getVoucherProductName(
                    $order->getId(),
                    $voucherProductSku
                ),
                'store' => $order->getStore(),
                'vouchers' => array_values($vouchers)
            ];
            $transportObject = new DataObject($transport);
            $this->vouchersEmailSender->send(
                $transportObject->getData(),
                $order->getCustomerEmail()
            );

            return true;
        } catch (\Exception $e) {
            $this->logger->info(__('[sendVouchersEmail] %1', $e->getMessage()));

            return false;
        }
    }
}
