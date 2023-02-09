<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Translation\Model\ResourceModel\StringUtils;

class AddTopoChicoBlockTranslations implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var StringUtils
     */
    private $stringUtils;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * UpdateHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Data $helper
     * @param StringUtils $stringUtils
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Data $helper,
        StringUtils $stringUtils,
        ResourceConnection $resourceConnection
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->helper = $helper;
        $this->stringUtils = $stringUtils;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|\Magento\Framework\Setup\Patch\DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

//        $this->addBlockTranslations();
        $this->addAttributeLabelTranslations();

        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    private function addBlockTranslations(): void
    {
        $topoChicoGrStoreId = $this->helper->getTopoChicoGreeceStore()->getId();
        $blockTranslations = [
            [
                'string' => 'Privacy Policy',
                'translate' => 'Πολιτική Προστασίας Δεδομένων',
                'locale' => 'el_GR',
                'store_id' => $topoChicoGrStoreId
            ],
            [
                'string' => 'Contact Us',
                'translate' => 'Επικοινώνησε μαζί μας',
                'locale' => 'el_GR',
                'store_id' => $topoChicoGrStoreId
            ]
        ];

        foreach ($blockTranslations as $blockTranslation) {
            $this->stringUtils->saveTranslate(
                $blockTranslation['string'],
                $blockTranslation['translate'],
                $blockTranslation['locale'],
                $blockTranslation['store_id']
            );
        }
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    private function addAttributeLabelTranslations()
    {
        $topoChicoGrStoreId = $this->helper->getTopoChicoGreeceStore()->getId();
        $attributeTranslations = [
            [
                'attribute_id' => $this->getAttributeIdByCode('brand'),
                'store_id' => $topoChicoGrStoreId,
                'value' => 'Brand'
            ],
            [
                'attribute_id' => $this->getAttributeIdByCode('pack_size'),
                'store_id' => $topoChicoGrStoreId,
                'value' => 'Συσκευασία'
            ]
        ];

        foreach ($attributeTranslations as $attributeTranslation) {
            $this->saveAttributeLabelTranslation(
                $attributeTranslation['attribute_id'],
                $attributeTranslation['store_id'],
                $attributeTranslation['value']
            );
        }
    }

    /**
     * @param string $code
     * @return string
     */
    private function getAttributeIdByCode(string $code)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('eav_attribute'),
            'attribute_id'
        )->where('attribute_code = ?', $code);

        return $connection->fetchOne($select);
    }

    /**
     * @param int $attributeId
     * @param int $storeId
     * @param string $value
     */
    private function saveAttributeLabelTranslation(int $attributeId, int $storeId, string $value)
    {
        $connection = $this->getConnection();
        $data = [
            'attribute_id' => $attributeId,
            'store_id' => $storeId,
            'value' => $value
        ];

        $connection->insert($connection->getTableName('eav_attribute_label'), $data);
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }
}
