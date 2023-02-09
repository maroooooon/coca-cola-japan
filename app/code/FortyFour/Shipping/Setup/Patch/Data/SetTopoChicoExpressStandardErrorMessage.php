<?php

namespace FortyFour\Shipping\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class SetTopoChicoExpressStandardErrorMessage implements DataPatchInterface
{
    const XML_PATH_EXPRESS_SPECIFICERRMSG = 'carriers/express/specificerrmsg';
    const XML_PATH_STANDARD_SPECIFICERRMSG = 'carriers/standard/specificerrmsg';
    const ENGLISH_SHIPPING_ERROR_MSG = 'We are SORRY, we are not delivering in your area... yet, but you can find Topo Chico in our partner <a href="https://eshop.mymarket.gr/mpyres-anapsyktika-krasia-pota/alkooloucha-pota/ready-to-drink" target="_blank">MyMarket</a>';
    const GREEK_SHIPPING_ERROR_MSG = 'Λυπούμαστε, δεν κάνουμε παραδόσεις στην περιοχή σου... ακόμα, αλλά μπορείς να βρεις τα προϊόντα Topo Chico στο <a href="https://eshop.mymarket.gr/mpyres-anapsyktika-krasia-pota/alkooloucha-pota/ready-to-drink" target="_blank">MyMarket</a>';

    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * AddTopoChicoEmailAddresses constructor.
     * @param WriterInterface $configWriter
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        WriterInterface $configWriter,
        StoreManagerInterface $storeManager
    ) {
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply()
    {
        $topoChicoGrEnStoreId = $this->getTopoChicoEnglishStore()->getId();
        $topoChicoGrGrStoreId = $this->getTopoChicoGreeceStore()->getId();

        $rows = [
            [
                'path' => self::XML_PATH_EXPRESS_SPECIFICERRMSG,
                'value' => self::ENGLISH_SHIPPING_ERROR_MSG,
                'store_id' => $topoChicoGrEnStoreId
            ],
            [
                'path' => self::XML_PATH_EXPRESS_SPECIFICERRMSG,
                'value' => self::GREEK_SHIPPING_ERROR_MSG,
                'store_id' => $topoChicoGrGrStoreId
            ],
            [
                'path' => self::XML_PATH_STANDARD_SPECIFICERRMSG,
                'value' => self::ENGLISH_SHIPPING_ERROR_MSG,
                'store_id' => $topoChicoGrEnStoreId
            ],
            [
                'path' => self::XML_PATH_STANDARD_SPECIFICERRMSG,
                'value' => self::GREEK_SHIPPING_ERROR_MSG,
                'store_id' => $topoChicoGrGrStoreId
            ]
        ];

        foreach ($rows as $row) {
            $this->setShippingErrorMessage($row['path'], $row['value'], $row['store_id']);
        }

        return $this;
    }

    /**
     * @param string $path
     * @param string $value
     * @param int $storeId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function setShippingErrorMessage(string $path, string $value, int $storeId): void
    {
        $this->configWriter->save($path, $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $storeId);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTopoChicoEnglishStore()
    {
        return $this->storeManager->getStore('topo_chico_gr_en');
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTopoChicoGreeceStore()
    {
        return $this->storeManager->getStore('topo_chico_gr_gr');
    }
}
