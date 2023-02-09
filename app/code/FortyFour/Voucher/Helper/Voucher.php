<?php

namespace FortyFour\Voucher\Helper;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class Voucher
{
    const SALES_ORDER_VOUCHERS_SENT_FLAG = 'vouchers_sent_to_customer';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;
    /**
     * @var Config
     */
    private $voucherConfig;

    /**
     * Voucher constructor.
     * @param LoggerInterface $logger
     * @param Config $voucherConfig
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LoggerInterface $logger,
        Config $voucherConfig,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->voucherConfig = $voucherConfig;
    }

    /**
     * @return int|null
     */
    public function isEnabled(): ?int
    {
        return $this->voucherConfig->isEnabled();
    }

    /**
     * @return array|null
     */
    public function getVoucherSkuQtyList(): ?array
    {
        return $this->voucherConfig->getVoucherSkuQtyList();
    }

    /**
     * @return array|null
     */
    public function getVoucherSkus(): ?array
    {
        return array_column($this->getVoucherSkuQtyList(), 'sku') ?? null;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param int $orderId
     * @return array|null
     */
    public function getProductSkusFromOrderById(int $orderId): ?array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['o' => $this->connection->getTableName('sales_order')],
            ['i.sku']
        )->join(
            ['i' => 'sales_order_item'],
            'o.entity_id = i.order_id',
            []
        )->where('o.entity_id = ?', $orderId);

        return $this->connection->fetchCol($select);
    }

    /**
     * @param string $sku
     * @param int $orderId
     * @return string|null
     */
    public function getQtyOrderedBySkuAndOrderId(string $sku, int $orderId): ?string
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['i' => $this->connection->getTableName('sales_order_item')],
            ['i.qty_ordered']
        )->where(
            'i.sku = ?', $sku
        )->where(
            'i.order_id = ?', $orderId
        );

        return $this->connection->fetchOne($select);
    }

    /**
     * @param string $sku
     * @param int $orderId
     * @return int
     */
    public function getNumberOfVouchersToSend(string $sku, int $orderId): int
    {
        $qtyOrdered = $this->getQtyOrderedBySkuAndOrderId($sku, $orderId);

        $voucherSkuQtyList = $this->getVoucherSkuQtyList();
        foreach ($voucherSkuQtyList as $item) {
            if ($item['sku'] === $sku) {
                return (int)($item['number_of_vouchers_to_send'] * $qtyOrdered);
            }
        }

        return 0;
    }

    /**
     * @param int $orderId
     * @param string $voucherProductSku
     * @return string|null
     */
    public function getVoucherProductName(int $orderId, string $voucherProductSku): ?string
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['i' => $this->connection->getTableName('sales_order_item')],
            ['i.name']
        )->where(
            'i.order_id = ?', $orderId
        )->where(
            'i.sku = ?', $voucherProductSku
        )->distinct(true);

        return $this->connection->fetchOne($select) ?? null;
    }

    /**
     * @param int $voucherCount
     * @return array|null
     */
    public function getVouchersToSendToCustomer(int $voucherCount): ?array
    {
        $salesRuleId = $this->voucherConfig->getCartPriceRuleId();

        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['c' => $this->connection->getTableName('salesrule_coupon')],
            ['c.coupon_id', 'c.code']
        )->join(
            ['s' => 'salesrule'],
            'c.rule_id = s.rule_id',
            []
        )->where(
            'c.rule_id = ?', $salesRuleId
        )->where(
            'c.sent_to_customer = 0'
        )->where(
            's.is_active = 1'
        )->order(
            'coupon_id ASC'
        )->limit($voucherCount);

        return $this->connection->fetchPairs($select) ?? null;
    }

    /**
     * @param array $couponIds
     * @return void
     */
    public function setCouponsSentToCustomerById(array $couponIds): void
    {
        $connection = $this->getConnection();
        $connection->update(
            $connection->getTableName('salesrule_coupon'),
            ['sent_to_customer' => '1'],
            ['coupon_id IN (?)' => $couponIds]
        );
    }

    /**
     * @param int $orderId
     * @return void
     */
    public function setOrderVouchersSentFlag(int $orderId): void
    {
        $connection = $this->getConnection();
        $connection->update(
            $connection->getTableName('sales_order'),
            [self::SALES_ORDER_VOUCHERS_SENT_FLAG => '1'],
            ['entity_id = ?' => $orderId]
        );
    }
}
