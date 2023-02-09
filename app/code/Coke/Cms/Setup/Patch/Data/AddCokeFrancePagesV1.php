<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCokeFrancePagesV1 implements DataPatchInterface
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
        $franceStore = $this->helper->getONLBFranceStore();
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->contentUpgrader->upgradePagesByStoreView([
            'france_home' => [
                'title' => 'Home - France',
                'content_heading' => '',
                'identifier' => 'home',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'france_conditions_util' => [
                'title' => "Conditions d'utilisation du site",
                'content_heading' => "Conditions d'utilisation du site",
                'identifier' => 'conditions-utilisation-site',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'france_cookies' => [
                'title' => "Politique de cookies Coca-Cola",
                'content_heading' => "Politique relative aux cookies",
                'identifier' => 'politique-de-cookies-tccc',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'france_politique' => [
                'title' => "Politique de confidentialité Coca-Cola",
                'content_heading' => "Politique de confidentialité",
                'identifier' => 'politique-confidentialite-tccc',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'france_conditions_general' => [
                'title' => "Conditions générales de vente",
                'content_heading' => "CONDITIONS GENERALES DE VENTE DE TESSI TMS -  BOUTEILLES PERSONNALISEES COCA-COLA",
                'identifier' => 'conditions-generales-de-vente',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'france_politique_tessi' => [
                'title' => "Politique d’utilisation des données personnelles de TESSI TMS",
                'content_heading' => "Politique d’utilisation des données personnelles de TESSI TMS",
                'identifier' => 'politique-confidentialite-tessi',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
            'france_contact' => [
                'title' => "Nous contacter",
                'content_heading' => "",
                'identifier' => 'nous-contacter',
                'stores' => [$franceStore->getId()],
                'page_layout' => 'cms-full-width',
                'is_active' => '1'
            ],
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
