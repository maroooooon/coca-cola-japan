<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpdateJapanMarcheMyLabelPagesV2 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
            'marche_mycoke_yourcoke' => [
                'title' => 'My Coke Your Coke',
                'content_heading' => '',
                'identifier' => 'my-coke-your-coke',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => 1
            ],
            'marche_mylabel' => [
                'title' => 'My Label',
                'content_heading' => '',
                'identifier' => 'my-label',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => 1
            ],
            'marche_hellolabel' => [
                'title' => 'Hello Label',
                'content_heading' => '',
                'identifier' => 'hello-label',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => 1
            ],
            'marche_thankslabel' => [
                'title' => 'Thanks Label',
                'content_heading' => '',
                'identifier' => 'thanks-label',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => 1
            ],

        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
