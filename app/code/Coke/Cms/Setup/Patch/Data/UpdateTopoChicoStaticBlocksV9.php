<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpdateTopoChicoStaticBlocksV9 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * UpdateTopoChicoStaticBlocks constructor.
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
        $this->moduleDataSetup->getConnection()->startSetup();
        $engStore = $this->helper->getTopoChicoEnglishStore();
        $grStore = $this->helper->getTopoChicoGreeceStore();

        $this->contentUpgrader->upgradeBlocks([
            'topochico_variety_pack_eng' => [
                'title' => 'TopoChico Variety Pack Eng',
                'stores' => [$engStore->getId()]
            ],
            'topochico_variety_pack_gr' => [
                'title' => 'TopoChico Variety Pack Gr',
                'stores' => [$grStore->getId()]
            ],
            'topochico-greece-banner-eng' => [
                'title' => 'TopoChico Greece Banner Eng',
                'stores' => [$engStore->getId()]
            ],
            'topochico-greece-banner-gr' => [
                'title' => 'TopoChico Greece Banner Gr',
                'stores' => [$grStore->getId()]
            ]
        ]);

        $this->contentUpgrader->upgradeBlocksByStoreView([
            'topochico_nav_additional_eng' => [
                'title' => 'TopoChico Nav Additional Eng',
                'store_id' => [$engStore->getId()],
                'stores' => [$engStore->getId()],
                'identifier' => 'topochico_nav_additional'
            ],
            'topochico_nav_additional_gr' => [
                'title' => 'TopoChico Nav Additional Gr',
                'store_id' => [$grStore->getId()],
                'stores' => [$engStore->getId()],
                'identifier' => 'topochico_nav_additional'
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
