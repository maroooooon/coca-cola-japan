<?php

namespace FortyFour\Shipping\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DisableDeliveryAfter48DeliveryWithin48 implements DataPatchInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * AddTopoChicoEmailAddresses constructor.
     * @param WriterInterface $configWriter
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection
    ) {
        $this->configWriter = $configWriter;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|DisableDeliveryAfter48DeliveryWithin48
     */
    public function apply()
    {
        $this->disableCokeDeliveryShippingMethods();
        return $this;
    }

    /**
     * @return void
     */
    private function disableCokeDeliveryShippingMethods(): void
    {
        $websiteId = $this->getTopoChicoGrWebsiteId();

        $rows = [
            'carriers/delivery_after_48/active' => 0,
            'carriers/delivery_within_48/active' => 0
        ];

        foreach ($rows as $path => $value) {
            $this->configWriter->save(
                $path,
                $value,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
                $websiteId
            );
        }
    }

    /**
     * @return string
     */
    private function getTopoChicoGrWebsiteId(): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store_website'),
            'website_id'
        )->where("code = 'topo_chico_gr_website'");

        return $connection->fetchOne($select);
    }
}
