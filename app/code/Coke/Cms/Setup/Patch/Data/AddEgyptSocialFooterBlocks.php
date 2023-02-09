<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class AddEgyptSocialFooterBlocks implements DataPatchInterface
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
     * AddEgyptSocialFooterBlocks constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->storeRepository = $storeRepository;
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
     * @return $this|DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $egyptAr = $this->storeRepository->get('egypt')->getId();
        $egyptEn = $this->storeRepository->get('egypt_en')->getId();

        $this->contentUpgrader->upgradeBlocks([
            'coke_egypt_footer_social_links_en' => [
                'title' => 'Coke Egypt Footer Social Links - English',
                'identifier' => 'coke_egypt_footer_social_links',
                'stores' => [$egyptEn]
            ],
            'coke_egypt_footer_social_links_ar' => [
                'title' => 'Coke Egypt Footer Social Links - Arabic',
                'identifier' => 'coke_egypt_footer_social_links',
                'stores' => [$egyptAr]
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
