<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class AddJapanMarcheGlobalStaticBlocks implements \Magento\Framework\Setup\Patch\DataPatchInterface
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

        // footer links left
        $this->contentUpgrader->upgradeBlocksByStoreView([
            'marche_global_banner' => [
                'title' => 'Japan Marche Global Banner',
                'identifier' => 'marche_global_banner',
                'store_id' => [$japanMarche],
                'stores' => [$japanMarche],
            ],
            'marche_nav_additional' => [
                'title' => 'Japan Marche Nav Additional',
                'identifier' => 'marche_nav_additional',
                'store_id' => [$japanMarche],
                'stores' => [$japanMarche],
            ],
            'marche_footer_content' => [
                'title' => 'Japan Marche Footer',
                'identifier' => 'marche_footer_content',
                'store_id' => [$japanMarche],
                'stores' => [$japanMarche],
            ],
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
