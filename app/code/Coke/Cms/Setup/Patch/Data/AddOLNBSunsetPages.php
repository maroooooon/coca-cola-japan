<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddOLNBSunsetPages implements DataPatchInterface
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
     * @return $this|DataPatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->contentUpgrader->upgradePagesByStoreView([

            'sunset_olnb_bel_lux_fr_lp' => [
                'title' => 'Thank You - OTB BEL FR',
                'content_heading' => '',
                'identifier' => 'thank-you',
                'stores' => [$this->helper->getOLNBBelgiumLuxembourgFrStore()->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'sunset_olnb_bel_lux_nl_lp' => [
                'title' => 'Thank You - OTB BEL NL',
                'content_heading' => '',
                'identifier' => 'thank-you',
                'stores' => [$this->helper->getOLNBBelgiumLuxembourgNlStore()->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'sunset_olnb_gb_lp' => [
                'title' => 'Thank You - OTB GB',
                'content_heading' => '',
                'identifier' => 'thank-you',
                'stores' => [$this->helper->getOLNBGreatBritainEnglishStore()->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'sunset_olnb_germany_lp' => [
                'title' => 'Thank You - OTB DE',
                'content_heading' => '',
                'identifier' => 'thank-you',
                'stores' => [$this->helper->getOLNBGermanyGermanStore()->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'sunset_olnb_nl_lp' => [
                'title' => 'Thank You - OTB NL',
                'content_heading' => '',
                'identifier' => 'thank-you',
                'stores' => [$this->helper->getOLNBNetherlandsDutchStore()->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],

            // 404
            'sunset_404_redirect_to_home' => [
                'title' => 'Thank You Redirect',
                'content_heading' => '',
                'identifier' => 'thank-you-no-route',
                'stores' => [
                    $this->helper->getOLNBBelgiumLuxembourgFrStore()->getId(),
                    $this->helper->getOLNBBelgiumLuxembourgNlStore()->getId(),
                    $this->helper->getOLNBGreatBritainEnglishStore()->getId(),
                    $this->helper->getOLNBGermanyGermanStore()->getId(),
                    $this->helper->getOLNBNetherlandsDutchStore()->getId()
                ],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ]
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
