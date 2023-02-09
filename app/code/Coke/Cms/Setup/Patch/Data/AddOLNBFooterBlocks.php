<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class AddOLNBFooterBlocks implements \Magento\Framework\Setup\Patch\DataPatchInterface
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

        $germany = $this->storeRepository->get('germany_german')->getId();
        $belgiumNL = $this->storeRepository->get('belgium_luxembourg_dutch')->getId();
        $belgiumFR = $this->storeRepository->get('belgium_luxembourg_french')->getId();
        $netherlands = $this->storeRepository->get('netherlands_dutch')->getId();
        $finland = $this->storeRepository->get('finland_finnish')->getId();
        $norway = $this->storeRepository->get('norway_norwegian')->getId();
        $ireland = $this->storeRepository->get('ireland_english')->getId();
        $northernIreland = $this->storeRepository->get('northern_ireland_english')->getId();
        $gb = $this->storeRepository->get('great_britain_english')->getId();

        // footer links left
        $this->contentUpgrader->upgradeBlocks([
            'olnb_footer_links_left' => [
                'title' => 'OLNB Footer Links Left',
                'identifier' => 'footer-links-left',
                'stores' => [
                    $germany, $belgiumFR, $belgiumNL, $netherlands, $finland,
                    $norway, $ireland, $northernIreland, $gb
                ]
            ],
            'olnb_footer_links_right' => [
                'title' => 'OLNB Footer Links Right',
                'identifier' => 'footer-links-right',
                'stores' => [
                    $germany, $belgiumFR, $belgiumNL, $netherlands, $finland,
                    $norway, $ireland, $northernIreland, $gb
                ]
            ],
            'olnb_footer_service' => [
                'title' => 'OLNB Footer Service',
                'identifier' => 'footer-service',
                'stores' => [
                    $germany, $belgiumFR, $belgiumNL, $netherlands, $finland,
                    $norway, $ireland, $northernIreland, $gb
                ]
            ],
            'olnb_footer_social_icons' => [
                'title' => 'OLNB Footer Social Icons',
                'identifier' => 'footer-social-icons',
                'stores' => [
                    $germany, $belgiumFR, $belgiumNL, $netherlands, $finland,
                    $norway, $ireland, $northernIreland, $gb
                ]
            ],
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
