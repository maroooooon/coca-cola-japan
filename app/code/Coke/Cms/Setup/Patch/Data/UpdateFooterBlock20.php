<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Widget\Model\Widget\InstanceFactory as WidgetFactory;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Framework\App\State;

class UpdateFooterBlock20 implements DataPatchInterface
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
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepositoryInterface;

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

    /**
     * UpdateHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepositoryInterface
     * @param PageFactory $pageFactory
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param Data $helper
     * @param WidgetFactory $widgetFactory
     * @param ThemeCollectionFactory $themeCollectionFactory
     * @param State $state
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepositoryInterface,
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepositoryInterface,
        Data $helper,
        WidgetFactory $widgetFactory,
        ThemeCollectionFactory $themeCollectionFactory,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->helper = $helper;
        $this->blockFactory = $blockFactory;
        $this->blockRepositoryInterface = $blockRepositoryInterface;
        $this->pageFactory = $pageFactory;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->widgetFactory = $widgetFactory;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->state = $state;
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

        $arStore = $this->helper->getEgyptArabicStore();
        $engStore = $this->helper->getEgyptEnglishStore();

        $this->createCmsPages($engStore, $arStore);

        /** Create block for English store view */
        /** @var BlockInterface $block */
        $block = $this->blockFactory->create();

        $block->setIdentifier('privacy-policy');
        $block->setContent($this->contentUpgrader->getContentFile('blocks', 'privacy-policy'));
        $block->setTitle('Privacy Policy Eng');
        $block->setData('stores', [$engStore->getId()]);
        $createdBlockEng = $this->blockRepositoryInterface->save($block);

        /** Create block for Arabic store view */
        /** @var BlockInterface $block */
        $block = $this->blockFactory->create();

        $block->setIdentifier('privacy-policy-ar');
        $block->setContent($this->contentUpgrader->getContentFile('blocks', 'privacy-policy-ar'));
        $block->setTitle('Privacy Policy Arabic');
        $block->setData('stores', [$arStore->getId()]);
        $createdBlockAr = $this->blockRepositoryInterface->save($block);

        $theme = $this->themeCollectionFactory->create();
        $themeId = $theme->getThemeByFullPath('frontend/Coke/egypt')->getId();

        $this->createWidget($engStore, 'Privacy Footer Link English', $themeId, $createdBlockEng->getId());
        $this->createWidget($arStore, 'Privacy Footer Link Arabic', $themeId, $createdBlockAr->getId());

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @param StoreInterface $engStore
     * @param StoreInterface $arStore
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createCmsPages($engStore, $arStore)
    {
        /** @var PageInterface $pageEng */
        $pageEng = $this->pageFactory->create();
        $pageEng->setIdentifier('privacy-policy');
        $pageEng->setContent($this->contentUpgrader->getContentFile('pages', 'privacy-policy'));
        $pageEng->setTitle('Privacy & Cookie Policy');
        $pageEng->setData('stores', [$engStore->getId()]);
        $this->pageRepositoryInterface->save($pageEng);

        /** @var PageInterface $pageAr */
        $pageAr = $this->pageFactory->create();
        $pageAr->setIdentifier('privacy-policy');
        $pageAr->setContent($this->contentUpgrader->getContentFile('pages', 'privacy-policy-ar'));
        $pageAr->setTitle('سياسة الخصوصية');
        $pageAr->setData('stores', [$arStore->getId()]);
        $this->pageRepositoryInterface->save($pageAr);
    }

    /**
     * @param StoreInterface $store
     * @param string $title
     * @param int $themeId
     * @param int $blockId
     * @throws \Exception
     */
    protected function createWidget($store, $title, $themeId, $blockId)
    {
        $widget = $this->widgetFactory->create();
        $widget->setType('Magento\Cms\Block\Widget\Block');
        $widget->setCode('cms_static_block');
        $widget->setThemeId($themeId);

        $pageGroup = [
            'page_group' => 'all_pages',
            'all_pages' => [
                'page_id' => null,
                'layout_handle' => 'default',
                'block' => 'cms_footer_links_container',
                'for' => 'all',
                'template' => 'widget/static_block/default.phtml'
            ]
        ];

        $widget->setTitle($title)
            ->setStoreIds([$store->getId()])
            ->setWidgetParameters(['block_id' => $blockId])
            ->setPageGroups([$pageGroup]);

        $this->state->emulateAreaCode(
            'frontend',
            [$widget, 'save']
        );
    }
}
