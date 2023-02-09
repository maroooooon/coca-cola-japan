<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpdateJapanMarcheBundlesCmsV0 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * @var Data
     */
    private $helper;

    /**
     * UpdateHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param Data $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        Data $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
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
        $japanMarche = $this->helper->getJapanMarcheJapaneseStore();
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->contentUpgrader->upgradePagesByStoreView([
            'marche_bundles_list' => [
                'title' => 'バンドル',
                'content_heading' => '',
                'identifier' => 'bundles-list',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width'
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
