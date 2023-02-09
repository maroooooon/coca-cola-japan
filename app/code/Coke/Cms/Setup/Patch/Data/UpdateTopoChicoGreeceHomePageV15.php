<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsBlockCollectionFactory;

class UpdateTopoChicoGreeceHomePageV15 implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var CmsBlockCollectionFactory
     */
    private $cmsBlockCollectionFactory;
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;
    /**
     * @var CmsPageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * UpdateTopoChicoGreeceHomePage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ContentUpgrader $contentUpgrader
     * @param Data $helper
     * @param PageFactory $pageFactory
     * @param CmsBlockCollectionFactory $cmsBlockCollectionFactory
     * @param PageRepositoryInterface $pageRepository
     * @param CmsPageCollectionFactory $pageCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ContentUpgrader $contentUpgrader,
        Data $helper,
        PageFactory $pageFactory,
        CmsBlockCollectionFactory $cmsBlockCollectionFactory,
        PageRepositoryInterface $pageRepository,
        CmsPageCollectionFactory $pageCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->contentUpgrader = $contentUpgrader;
        $this->helper = $helper;
        $this->pageFactory = $pageFactory;
        $this->cmsBlockCollectionFactory = $cmsBlockCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            CreateTopoChicoGreeceHomePage::class,
        ];
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
        $engStore = $this->helper->getTopoChicoEnglishStore();
        $grStore = $this->helper->getTopoChicoGreeceStore();
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->contentUpgrader->upgradePagesByStoreview([
            'home_topochico_greece_eng' => [
                'title' => 'TopoChico Greece Homepage - Eng',
                'stores' => [$engStore->getId()],
                'identifier' => 'home',
                'page_layout' => 'cms-full-width'
            ],
            'home_topochico_greece_gr' => [
                'title' => 'TopoChico Greece Homepage - Gr',
                'stores' => [$grStore->getId()],
                'identifier' => 'home',
                'page_layout' => 'cms-full-width'
            ],
        ], function ($content) {
            return str_replace([
                '{{VARIETY_PACK_ENG}}',
                '{{VARIETY_PACK_GR}}'
            ], [
                sprintf(
                    '{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" ' .
                    'block_id="%s"}}',
                    $this->getCmsBlockIdByIdentifier('topochico_variety_pack_eng')
                ),
                sprintf(
                    '{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" ' .
                    'block_id="%s"}}',
                    $this->getCmsBlockIdByIdentifier('topochico_variety_pack_gr')
                )
            ], $content);
        });




        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @param string $identifier
     * @return int
     */
    public function getCmsBlockIdByIdentifier(string $identifier): int
    {
        $cmsBlockCollection = $this->cmsBlockCollectionFactory->create();
        $cmsBlockCollection->addFieldToFilter('identifier', $identifier)
            ->addFieldToSelect('block_id');
        $cmsBlockCollection = $cmsBlockCollection->getFirstItem();

        return $cmsBlockCollection->getId();
    }
}
