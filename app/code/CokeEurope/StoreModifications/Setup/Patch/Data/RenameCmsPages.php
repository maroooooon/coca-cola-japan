<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class RenameCmsPages implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /** @var CollectionFactory */
    protected $pageCollectionFactory;

    /** @var SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /**
     * @param CollectionFactory $pageCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionFactory $pageCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $cmsPages = $this->pageCollectionFactory->create()
            ->addFieldToFilter('title', ['like' => '%OLNB -%']);

        /** @var \Magento\Cms\Model\Page $cmsPage */
        foreach ($cmsPages as $cmsPage) {
            $cmsPage->setTitle(str_replace('OLNB -', 'Coke Europe -', $cmsPage->getTitle()));
            $cmsPage->setContent(''); //Reset content
            $cmsPage->setMetaTitle('Coke Europe - Home');
        }

        $cmsPages->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
