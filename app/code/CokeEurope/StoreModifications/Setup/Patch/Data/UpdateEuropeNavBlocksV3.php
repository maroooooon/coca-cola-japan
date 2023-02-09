<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use CokeEurope\StoreModifications\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class UpdateEuropeNavBlocksV3 implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var Data
     */
    private $helper;

    /**
     * UpdateEuropeNavBlocksV2 constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param StoreRepositoryInterface $storeRepository
     * @param Data $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        StoreRepositoryInterface $storeRepository,
        Data $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->storeRepository = $storeRepository;
        $this->helper = $helper;
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

        $storeArr = [];
        $s1 = $this->helper->getEuropeIrelandEnglishStore()->getId();
        $s2 = $this->helper->getEuropeNetherlandsDutchStore()->getId();
        $s3 = $this->helper->getEuropeFinlandFinnishStore()->getId();
        $s4 = $this->helper->getEuropeGermanyGermanStore()->getId();
        $s5 = $this->helper->getEuropeFranceFrenchStore()->getId();
        $s6 = $this->helper->getEuropeBelgiumFrenchStore()->getId();
        $s7 = $this->helper->getEuropeBelgiumDutchStore()->getId();
        $s8 = $this->helper->getUkIrelandEnglishStore()->getId();
        $s9 = $this->helper->getUkGreatBritainEnglishStore()->getId();
        array_push($storeArr, $s1, $s2, $s3, $s4, $s5, $s6, $s7, $s8, $s9);

        foreach ($storeArr as $storeId) {
            $this->contentUpgrader->upgradeBlocksByStoreView([
                //Nav
                'eu_nav_additional' => [
                    'identifier' => 'eu_nav_additional',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                ]
            ]);
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
