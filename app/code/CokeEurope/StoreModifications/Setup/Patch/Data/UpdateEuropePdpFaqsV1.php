<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use CokeEurope\StoreModifications\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class UpdateEuropePdpFaqsV1 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * UpdateEuropePdpFaqsV1 constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param StoreRepositoryInterface $storeRepository
     * @param Data $helper
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        StoreRepositoryInterface $storeRepository,
        Data $helper,
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->storeRepository = $storeRepository;
        $this->helper = $helper;
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        //Remove old block(s)
        $this->searchCriteriaBuilder->addFilter('identifier', 'personalized_product_faq');
        try{
            $blocks = $this->blockRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            foreach ($blocks as $block){
                $this->blockRepository->delete($block);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
        }

        $s1 = $this->helper->getEuropeIrelandEnglishStore()->getId();
        $s2 = $this->helper->getEuropeNetherlandsDutchStore()->getId();
        $s3 = $this->helper->getEuropeGermanyGermanStore()->getId();
        $s4 = $this->helper->getEuropeFranceFrenchStore()->getId();
        $s5 = $this->helper->getEuropeBelgiumFrenchStore()->getId();
        $s6 = $this->helper->getEuropeBelgiumDutchStore()->getId();
        $s7 = $this->helper->getUkIrelandEnglishStore()->getId();
        $s8 = $this->helper->getUkGreatBritainEnglishStore()->getId();

        //English
        $storeArrUK = [$s1, $s7, $s8];
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'personalized_product_faq_en_US' => [
                'identifier' => 'personalized_product_faq',
                'title' => 'Europe Frequently Asked Questions',
                'stores' => $storeArrUK,
                'store_id' => $storeArrUK,
            ]
        ]);

        //French
        $storeArrFR = [$s4, $s5];
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'personalized_product_faq_fr_FR' => [
                'identifier' => 'personalized_product_faq',
                'title' => 'Europe Frequently Asked Questions',
                'stores' => $storeArrFR,
                'store_id' => $storeArrFR,
            ]
        ]);

        //Dutch
        $storeArrNL = [$s2, $s6];
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'personalized_product_faq_nl_BE' => [
                'identifier' => 'personalized_product_faq',
                'title' => 'Europe Frequently Asked Questions',
                'stores' => $storeArrNL,
                'store_id' => $storeArrNL,
            ]
        ]);

        //German
        $storeArrDE = [$s3];
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'personalized_product_faq_de_DE' => [
                'identifier' => 'personalized_product_faq',
                'title' => 'Europe Frequently Asked Questions',
                'stores' => $storeArrDE,
                'store_id' => $storeArrDE,
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}




