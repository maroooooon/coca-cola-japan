<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Coke\Cms\Model\ContentUpgrader;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsBlockCollectionFactory;
use Magento\Store\Api\Data\StoreInterface;

class CreateTopoChicoGreeceTermsConditionsPage implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
        $engStore = $this->helper->getTopoChicoEnglishStore();
        $grStore = $this->helper->getTopoChicoGreeceStore();
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->createCmsPages($engStore, $grStore);

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @param $engStore
     * @param $grStore
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createCmsPages($engStore, $grStore)
    {
        /** @var PageInterface $pageEng */
        $pageEng = $this->pageFactory->create();
        $pageEng->setIdentifier('terms-and-conditions');
        $pageEng->setTitle('Website Terms of Use');
        $pageEng->setData('stores', [$engStore->getId()]);
        $this->pageRepository->save($pageEng);

        /** @var PageInterface $pageGr */
        $pageGr = $this->pageFactory->create();
        $pageGr->setIdentifier('terms-and-conditions');
        $pageGr->setTitle('Όροι Χρήσης Ιστοσελίδας');
        $pageGr->setData('stores', [$grStore->getId()]);
        $this->pageRepository->save($pageGr);
    }
}
