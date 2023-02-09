<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class AddCokeFranceCmsBlocksV5 implements DataPatchInterface
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
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return $this|DataPatchInterface
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $france = $this->storeRepository->get('france_d2c')->getId();

        $this->contentUpgrader->upgradeBlocks([
            'france_header_promo' => [
                'title' => 'Header Promo - France',
                'identifier' => 'header-promo',
                'stores' => [$france]
            ],
            'france_footer_links_left' => [
                'title' => 'Left Footer Links - France',
                'identifier' => 'footer-links-left',
                'stores' => [$france]
            ],
            'france_footer_links_right' => [
                'title' => 'Right Footer Links - France',
                'identifier' => 'footer-links-right',
                'stores' => [$france]
            ],
            'france_footer_bottom' => [
                'title' => 'Footer Bottom - France',
                'identifier' => 'footer-bottom',
                'stores' => [$france]
            ],
            'france_category_grid' => [
                'title' => 'Category Grid - France',
                'identifier' => 'category-grid',
                'stores' => [$france]
            ],
            'france_resolutions' => [
                'title' => 'Resolutions - France',
                'identifier' => 'resolutions-france',
                'stores' => [$france]
            ],
            'france_celebrate' => [
                'title' => 'Celebrate - France',
                'identifier' => 'celebrate-france',
                'stores' => [$france]
            ],
            'france_cans_carousel' => [
                'title' => 'Cans Carousel - France',
                'identifier' => 'cans-carousel',
                'stores' => [$france]
            ],
            'france_bulk' => [
                'title' => 'Bulk - France',
                'identifier' => 'bulk-france',
                'stores' => [$france]
            ],
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
