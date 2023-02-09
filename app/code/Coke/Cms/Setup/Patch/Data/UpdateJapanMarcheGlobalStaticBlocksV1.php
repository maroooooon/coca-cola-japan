<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class UpdateJapanMarcheGlobalStaticBlocksV1 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * UpdateTopoChicoStaticBlocks constructor.
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
     * @return $this|\Magento\Framework\Setup\Patch\DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $japanMarche = $this->storeRepository->get('jp_marche_ja')->getId();

        $this->contentUpgrader->upgradeBlocksByStoreView([
            'marche_global_banner' => [
                'title' => 'Japan Marche Global Banner',
                'identifier' => 'marche_global_banner',
                'stores' => [$japanMarche],
                'store_id' => [$japanMarche],
            ],
            'marche_footer_content' => [
                'title' => 'Japan Marche Footer',
                'identifier' => 'marche_footer_content',
                'stores' => [$japanMarche],
                'store_id' => [$japanMarche],
            ],
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
