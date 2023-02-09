<?php

namespace FortyFour\Email\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddTopoChicoEmailAddressesV2 implements DataPatchInterface
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
     * @return $this|AddTopoChicoGrEmailLogo
     */
    public function apply()
    {
        $this->addTopoChicoGrEmailAddresses();
        return $this;
    }

    /**
     * @return void
     */
    private function addTopoChicoGrEmailAddresses(): void
    {
        $websiteId = $this->getTopoChicoGrWebsiteId();

        $emailAddresses = [
            'trans_email/ident_support/email' => 'greece.cic@coca-cola.com',
            'trans_email/ident_support/name' =>  'Topo Chico Hard Seltzer Greece',
            'trans_email/ident_sales/email' => 'sales@topochico.gr',
            'trans_email/ident_sales/name' => 'Topo Chico Hard Seltzer Greece',
            'trans_email/ident_general/email' => 'sales@topochico.gr',
            'trans_email/ident_general/name' => 'Topo Chico Hard Seltzer Greece'
        ];

        foreach ($emailAddresses as $path => $value) {
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
