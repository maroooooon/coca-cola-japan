<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use CokeEurope\StoreModifications\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class UpdateEuropeFooterBlocksV1 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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

        //UK, Ireland
        $storeArrUK = [$s1, $s7, $s8];
        foreach ($storeArrUK as $storeId) {
            $this->contentUpgrader->upgradeBlocksByStoreView([
                'eu_footer_links' => [
                    'identifier' => 'eu_footer_links',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                ]
            ], function ($content){
                return str_replace([
                    '%1',
                    '%2',
                    '%3'
                ],[
                    'https://www.coca-cola.co.uk/terms-of-use',
                    'https://www.coca-cola.co.uk/cookie-policy',
                    'https://www.coca-cola.co.uk/privacy-policy'
                ], $content);
            });
        }

        //NL
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'eu_footer_links' => [
                'identifier' => 'eu_footer_links',
                'stores' => [$s2],
                'store_id' => [$s2],
            ]
        ], function ($content){
            return str_replace([
                '%1',
                '%2',
                '%3'
            ],[
                'https://www.cocacolanederland.nl/voorwaarden',
                'https://www.cocacolanederland.nl/cookies',
                'https://www.cocacolanederland.nl/privacy'
            ], $content);
        });

        //BE Dutch
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'eu_footer_links' => [
                'identifier' => 'eu_footer_links',
                'stores' => [$s6],
                'store_id' => [$s6],
            ]
        ], function ($content){
            return str_replace([
                '%1',
                '%2',
                '%3'
            ],[
                'https://nl.coca-cola.be/gebruiksvoorwaarden',
                'https://nl.coca-cola.be/cookie-beleid',
                'https://nl.coca-cola.be/privacybeleid'
            ], $content);
        });

        //BE French
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'eu_footer_links' => [
                'identifier' => 'eu_footer_links',
                'stores' => [$s5],
                'store_id' => [$s5],
            ]
        ], function ($content){
            return str_replace([
                '%1',
                '%2',
                '%3'
            ],[
                'https://fr.coca-cola.be/conditions-dutilisation',
                'https://fr.coca-cola.be/politique-des-cookies',
                'https://fr.coca-cola.be/politique-de-confidentialite'
            ], $content);
        });

        //FR
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'eu_footer_links' => [
                'identifier' => 'eu_footer_links',
                'stores' => [$s4],
                'store_id' => [$s4],
            ]
        ], function ($content){
            return str_replace([
                '%1',
                '%2',
                '%3'
            ],[
                'https://www.coca-cola-france.fr/conditions-d-utilisation',
                'https://www.coca-cola-france.fr/CookiePolicy',
                'https://www.coca-cola-france.fr/politique-de-confidentialite'
            ], $content);
        });

        //DE
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'eu_footer_links' => [
                'identifier' => 'eu_footer_links',
                'stores' => [$s3],
                'store_id' => [$s3],
            ]
        ], function ($content){
            return str_replace([
                '%1',
                '%2',
                '%3'
            ],[
                'https://www.coca-cola-deutschland.de/datenschutz',
                'https://www.coca-cola-deutschland.de/cookie-policy',
                'https://www.coca-cola-deutschland.de/uber-uns/unternehmen/coca-cola-in-deutschland/nutzungsbedingungen'
            ], $content);
        });

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}




