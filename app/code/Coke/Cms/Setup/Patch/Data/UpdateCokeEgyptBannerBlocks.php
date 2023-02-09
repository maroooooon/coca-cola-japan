<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateCokeEgyptBannerBlocks implements DataPatchInterface
{
    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;
    /**
     * @var Data
     */
    private $helper;

    /**
     * UpdateCokeEgyptBannerBlocks constructor.
     * @param ContentUpgrader $contentUpgrader
     * @param Data $helper
     */
    public function __construct(
        ContentUpgrader $contentUpgrader,
        Data $helper
    ) {
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $arStore = $this->helper->getEgyptArabicStore();
        $engStore = $this->helper->getEgyptEnglishStore();

        $this->contentUpgrader->upgradeBlocks([
            'banner-eng' => [
                'stores' => [$engStore->getId()],
                'title' => 'Banner Eng'
            ],
        ]);

        $this->contentUpgrader->upgradeBlocks([
            'banner-ar' => [
                'stores' => [$arStore->getId()],
                'title' => 'Banner Ar'
            ],
        ]);

        return $this;
    }
}
