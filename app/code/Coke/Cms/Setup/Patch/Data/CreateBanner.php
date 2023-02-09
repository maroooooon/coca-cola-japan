<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Widget\Model\Widget\InstanceFactory as WidgetFactory;

class CreateBanner implements DataPatchInterface
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
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepositoryInterface;

    /**
     * @var WidgetFactory
     */
    private $widgetFactory;

    /**
     * @var ThemeCollectionFactory
     */
    private $themeCollectionFactory;

    /**
     * @var State
     */
    private $state;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepositoryInterface,
        Data $helper,
        WidgetFactory $widgetFactory,
        ThemeCollectionFactory $themeCollectionFactory,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->blockFactory = $blockFactory;
        $this->blockRepositoryInterface = $blockRepositoryInterface;
        $this->helper = $helper;
        $this->widgetFactory = $widgetFactory;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->state = $state;
    }


    /**
     * @return $this|DataPatchInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $arStore = $this->helper->getEgyptArabicStore();
        $engStore = $this->helper->getEgyptEnglishStore();

        $bannerEng = $this->contentUpgrader->upgradeBlocks([
            'banner-eng' => [
                'stores' => [$engStore->getId()],
                'title' => 'Banner Eng'
            ],
        ]);

        $bannerAr = $this->contentUpgrader->upgradeBlocks([
            'banner-ar' => [
                'stores' => [$arStore->getId()],
                'title' => 'Banner Ar'
            ],
        ]);

        $theme = $this->themeCollectionFactory->create();
        $themeId = $theme->getThemeByFullPath('frontend/Coke/egypt')->getId();

        $pageGroup = [
            [
                'page_group' => 'pages',
                'pages' => [
                    'layout_handle' => 'cms_page_view',
                    'block' => 'page.top',
                    'for' => 'all',
                    'template' => 'widget/static_block/default.phtml',
                    'page_id' => ''
                ]
            ],
            [
                'page_group' => 'all_products',
                'all_products' => [
                    'layout_handle' => 'catalog_product_view',
                    'block' => 'top.container',
                    'for' => 'all',
                    'template' => 'widget/static_block/default.phtml',
                    'page_id' => ''
                ]
            ],
            [
                'page_group' => 'anchor_categories',
                'anchor_categories' => [
                    'layout_handle' => 'catalog_category_view_type_layered',
                    'block' => 'top.container',
                    'for' => 'all',
                    'template' => 'widget/static_block/default.phtml',
                    'page_id' => ''
                ]
            ],
            [
                'page_group' => 'notanchor_categories',
                'notanchor_categories' => [
                    'layout_handle' => 'catalog_category_view_type_default',
                    'block' => 'top.container',
                    'for' => 'all',
                    'template' => 'widget/static_block/default.phtml',
                    'page_id' => ''
                ]
            ],
        ];

        $theme = $this->themeCollectionFactory->create();
        $themeId = $theme->getThemeByFullPath('frontend/Coke/egypt')->getId();

        $widget = $this->widgetFactory->create();
        $widget->setType('Magento\Cms\Block\Widget\Block');
        $widget->setCode('cms_static_block');
        $widget->setThemeId($themeId);

        $widget->setTitle('Widget Banner Eng')
            ->setStoreIds([$engStore->getId()])
            ->setWidgetParameters(['block_id' => $bannerEng[0]->getId()])
            ->setPageGroups($pageGroup);

        $this->state->emulateAreaCode(
            'frontend',
            [$widget, 'save']
        );

        $themeAr = $this->themeCollectionFactory->create();
        $themeArId = $themeAr->getThemeByFullPath('frontend/Coke/egypt_rtl')->getId();

        $widget = $this->widgetFactory->create();
        $widget->setType('Magento\Cms\Block\Widget\Block');
        $widget->setCode('cms_static_block');
        $widget->setThemeId($themeArId);

        $widget->setTitle('Widget Banner Ar')
            ->setStoreIds([$arStore->getId()])
            ->setWidgetParameters(['block_id' => $bannerAr[0]->getId()])
            ->setPageGroups($pageGroup);

        $this->state->emulateAreaCode(
            'frontend',
            [$widget, 'save']
        );

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
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
}
