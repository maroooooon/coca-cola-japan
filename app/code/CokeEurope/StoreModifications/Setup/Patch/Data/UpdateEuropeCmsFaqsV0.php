<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use CokeEurope\StoreModifications\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class UpdateEuropeCmsFaqsV0 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * UpdateEuropeCmsFaqs constructor.
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

        //UK English
        $storeArrEng = [$s1, $s7, $s8];
        foreach ($storeArrEng as $storeId) {
            $this->contentUpgrader->upgradePagesByStoreView([
                'faqs_en_US' => [
                    'identifier' => 'faqs',
                    'title' => 'Faq',
                    'content_heading' => 'Faq',
                    'stores' => [$storeId],
                    'store_id' => [$storeId],
                    'page_layout' => 'cms-full-width'
                ]
            ]);
        }

        //NL Dutch
        $this->contentUpgrader->upgradePagesByStoreView([
            'faqs_nl_BE' => [
                'identifier' => 'faqs',
                'title' => 'Faq',
                'content_heading' => 'Faq',
                'stores' => [$s6],
                'store_id' => [$s6],
                'page_layout' => 'cms-full-width'
            ]
        ], function ($content){
            return str_replace([
                '%1'
            ],[
                'https://www.cocacolanederland.nl/privacy'
            ], $content);
        });

        //Belgium Dutch
        $this->contentUpgrader->upgradePagesByStoreView([
            'faqs_nl_BE' => [
                'identifier' => 'faqs',
                'title' => 'Faq',
                'content_heading' => 'Faq',
                'stores' => [$s2],
                'store_id' => [$s2],
                'page_layout' => 'cms-full-width'
            ]
        ], function ($content){
            return str_replace([
                '%1'
            ],[
                'https://nl.coca-cola.be/privacybeleid'
            ], $content);
        });

        //Belgium French
        $this->contentUpgrader->upgradePagesByStoreView([
            'faqs_fr_FR' => [
                'identifier' => 'faqs',
                'title' => 'Faq',
                'content_heading' => 'Faq',
                'stores' => [$s5],
                'store_id' => [$s5],
                'page_layout' => 'cms-full-width'
            ]
        ], function ($content){
            return str_replace([
                '%1'
            ],[
                'https://fr.coca-cola.be/politique-de-confidentialite'
            ], $content);
        });

        //DE German
        $this->contentUpgrader->upgradePagesByStoreView([
            'faqs_de_DE' => [
                'identifier' => 'faqs',
                'title' => 'Faq',
                'content_heading' => 'Faq',
                'stores' => [$s3],
                'store_id' => [$s3],
                'page_layout' => 'cms-full-width'
            ]
        ], function ($content){
            return str_replace([
                '%1'
            ],[
                'https://www.coca-cola-deutschland.de/datenschutz'
            ], $content);
        });

        //FR French
        $this->contentUpgrader->upgradePagesByStoreView([
            'faqs_fr_FR' => [
                'identifier' => 'faqs',
                'title' => 'Faq',
                'content_heading' => 'Faq',
                'stores' => [$s4],
                'store_id' => [$s4],
                'page_layout' => 'cms-full-width'
            ]
        ], function ($content){
            return str_replace([
                '%1'
            ],[
                'https://www.coca-cola-france.fr/politique-de-confidentialite'
            ], $content);
        });

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
