<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Widget\Model\Widget\InstanceFactory as WidgetFactory;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Framework\App\State;

class UpdateContactPage implements DataPatchInterface
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

    /**
     * UpdateHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepositoryInterface
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

        $this->contentUpgrader->upgradeBlocks([
            'contact-us-info' => [
                'stores' => [$engStore->getId()],
                'title' => 'Contact Us Info'
            ],
        ]);

        /** @var BlockInterface $block */
        $block = $this->blockFactory->create();

        $block->setIdentifier('contact-us-info-ar');
        $block->setContent($this->contentUpgrader->getContentFile('blocks', 'contact-us-info-ar'));
        $block->setTitle('Contact us info Arabic');
        $block->setData('stores', [$arStore->getId()]);

        $createdBlock = $this->blockRepositoryInterface->save($block);

        $theme = $this->themeCollectionFactory->create();
        $themeId = $theme->getThemeByFullPath('frontend/Coke/egypt')->getId();

        $widget = $this->widgetFactory->create();
        $widget->setType('Magento\Cms\Block\Widget\Block');
        $widget->setCode('cms_static_block');
        $widget->setThemeId($themeId);

        $pageGroup = [
            'page_group' => 'pages',
            'pages' => [
                'layout_handle' => 'contact_index_index',
                'block' => 'content.top',
                'for' => 'all',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => ''
            ]
        ];

        $widget->setTitle('Contact Us Info Arabic')
            ->setStoreIds([$arStore->getId()])
            ->setWidgetParameters(['block_id' => $createdBlock->getId()])
            ->setPageGroups([$pageGroup]);

        $this->state->emulateAreaCode(
            'frontend',
            [$widget, 'save']
        );

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }
}
