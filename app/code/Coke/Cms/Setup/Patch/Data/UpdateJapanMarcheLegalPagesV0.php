<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpdateJapanMarcheLegalPagesV0 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
            'marche_company' => [
                'title' => '特定商取引法における表示',
                'content_heading' => '特定商取引法における表示',
                'identifier' => 'company-policy',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => '1column'
            ],
            'marche_membership_policy' => [
                'title' => '会員規約',
                'content_heading' => '会員規約',
                'identifier' => 'membership-policy',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => '1column'
            ],
            'marche_terms_and_conditions' => [
                'title' => '利用規約',
                'content_heading' => '利用規約',
                'identifier' => 'terms-conditions',
                'stores' => [$japanMarche->getId()],
                'store_id' => [$japanMarche->getId()],
                'page_layout' => '1column'
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
