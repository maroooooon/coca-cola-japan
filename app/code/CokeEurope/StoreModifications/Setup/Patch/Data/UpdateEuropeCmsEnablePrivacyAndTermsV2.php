<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use CokeEurope\StoreModifications\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class UpdateEuropeCmsEnablePrivacyAndTermsV2 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * UpdateEuropeFooterBlocksV0 constructor.
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

        $s1 = $this->helper->getEuropeIrelandEnglishStore()->getId();
        $s2 = $this->helper->getEuropeNetherlandsDutchStore()->getId();
        $s3 = $this->helper->getEuropeGermanyGermanStore()->getId();
        $s4 = $this->helper->getEuropeFranceFrenchStore()->getId();
        $s5 = $this->helper->getEuropeBelgiumFrenchStore()->getId();
        $s6 = $this->helper->getEuropeBelgiumDutchStore()->getId();
        $s7 = $this->helper->getUkIrelandEnglishStore()->getId();
        $s8 = $this->helper->getUkGreatBritainEnglishStore()->getId();

        //English
        $storeArrEng = [$s1, $s7, $s8];
        foreach ($storeArrEng as $storeId) {
            $this->contentUpgrader->upgradePagesByStoreView([
                'innovinity-privacy-policy_en_US' => [
                    'identifier' => 'innovinity-privacy-policy',
                    'title' => 'Innovinity Privacy Policy',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ],
                'innovinity-terms-of-use_en_US' => [
                    'identifier' => 'innovinity-terms-of-use',
                    'title' => 'Innovinity Conditions of Sale',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ]
            ]);
        }

        //Dutch
        $storeArrDut = [$s2, $s6];
        foreach ($storeArrDut as $storeId) {
            $this->contentUpgrader->upgradePagesByStoreView([
                'innovinity-privacy-policy_nl_BE' => [
                    'identifier' => 'innovinity-privacy-policy',
                    'title' => 'Innovinity Privacy Policy',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ],
                'innovinity-terms-of-use_nl_BE' => [
                    'identifier' => 'innovinity-terms-of-use',
                    'title' => 'Innovinity Conditions of Sale',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ]
            ]);
        }

        //French
        $storeArrFr = [$s4, $s5];
        foreach ($storeArrFr as $storeId) {
            $this->contentUpgrader->upgradePagesByStoreView([
                'innovinity-privacy-policy_fr_FR' => [
                    'identifier' => 'innovinity-privacy-policy',
                    'title' => 'Innovinity Privacy Policy',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ],
                'innovinity-terms-of-use_fr_FR' => [
                    'identifier' => 'innovinity-terms-of-use',
                    'title' => 'Innovinity Conditions of Sale',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ]
            ]);
        }

        //German
        $storeArrGer = [$s3];
        foreach ($storeArrGer as $storeId) {
            $this->contentUpgrader->upgradePagesByStoreView([
                'innovinity-privacy-policy_de_DE' => [
                    'identifier' => 'innovinity-privacy-policy',
                    'title' => 'Innovinity Privacy Policy',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ],
                'innovinity-terms-of-use_de_DE' => [
                    'identifier' => 'innovinity-terms-of-use',
                    'title' => 'Innovinity Conditions of Sale',
                    'content_heading' => '',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => '1column'
                ]
            ]);
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}




