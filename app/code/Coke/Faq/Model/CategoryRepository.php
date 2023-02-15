<?php

namespace Coke\Faq\Model;

class CategoryRepository
    implements \Coke\Faq\Api\CategoryRepositoryInterface
{
    /**
     * @var []
     */
    protected $validOrders = ["ASC", "DESC"];
    
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder 
     */
    protected $searchCriteriaBuilder;
    
    /**
     * @var \Magento\Framework\Api\FilterBuilder 
     */
    protected $filterBuilder;    
    
    /**
     * @var \Magento\Framework\Api\SortOrder 
     */
    protected $sortOrder;
    
    /**
     * @var \Magento\Framework\Api\SearchResultsFactory 
     */
    protected $searchResultsFactory;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Coke\Faq\Model\ResourceModel\Item\CollectionFactory 
     */
    protected $collectionFactory;
    
    /**
     * @var \Coke\Faq\Model\CategoryFactory
     */
    protected $categoryFactory;
    
    /** 
     * @var \Magento\Store\Model\StoreManagerInterface  
     */
    protected $storeManager;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrder $sortOrder,
        \Magento\Framework\Api\SearchResultsFactory $searchResultsFactory,
        \Coke\Faq\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,    
        \Coke\Faq\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ){
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;        
        $this->sortOrder = $sortOrder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $category = $this->categoryFactory->create();
        
        return $category;
    }
    
    /**
     * {@inheritdoc}
     */    
    public function get($categoryId)
    {
        $category = $this->categoryFactory->create()
                                          ->load($categoryId);
        
        return $category;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getActiveCategories($order = false)
    {
        // Active filter
        $activeFilter = $this->filterBuilder
                             ->setField('is_active')
                             ->setValue(1)
                             ->setConditionType('eq')
                             ->create();
        
        // Add filters.
        $this->searchCriteriaBuilder->addFilters([$activeFilter]);
        
        // Validate order
        if (($order !== false) && (in_array(strtoupper($order), $this->validOrders))) {
            // Add sort order
            $orderCriteria = $this->sortOrder->setField('name')
                                             ->setDirection($order);
            
            $this->searchCriteriaBuilder->addSortOrder($orderCriteria);
        }
        
        // Build searchCriteria object with filters
        $searchCriteria = $this->searchCriteriaBuilder->create();        
        
        // Get active categories for current store
        return $this->getList($searchCriteria);        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getActiveCategoriesForCurrentStore($order = false)
    {
        // Store filter
        $storeFilter = $this->filterBuilder
                            ->setField('store_id')
                            ->setValue($this->storeManager->getStore()->getId())
                            ->setConditionType('eq')
                            ->create();
        
        // Active filter
        $activeFilter = $this->filterBuilder
                             ->setField('is_active')
                             ->setValue(1)
                             ->setConditionType('eq')
                             ->create();
        
        // Add filters. If you add in the same array, you will get an OR filter.
        // If you do it like this, you get an AND filter
        $this->searchCriteriaBuilder->addFilters([$storeFilter]);
        $this->searchCriteriaBuilder->addFilters([$activeFilter]);
        
        // Validate order
        if (($order !== false) && (in_array(strtoupper($order), $this->validOrders))) {
            // Add sort order
            $orderCriteria = $this->sortOrder->setField('sort_order')
                                             ->setDirection($order);
            
            $this->searchCriteriaBuilder->addSortOrder($orderCriteria);
        }
        
        // Build searchCriteria object with filters
        $searchCriteria = $this->searchCriteriaBuilder->create();        
        
        // Get active categories for current store
        return $this->getList($searchCriteria);        
    }
    
    /**
     * {@inheritdoc}
     */    
    public function getByUrlKey($urlKey)
    {
        // Url key filter
        $urlKeyFilter = $this->filterBuilder
                             ->setField('url_key')
                             ->setValue($urlKey)
                             ->setConditionType('eq')
                             ->create();

        // Active filter
        $activeFilter = $this->filterBuilder
                             ->setField('is_active')
                             ->setValue(1)
                             ->setConditionType('eq')
                             ->create();
        
        // Store filter
        $storeFilter = $this->filterBuilder
                            ->setField('store_id')
                            ->setValue($this->storeManager->getStore()->getId())
                            ->setConditionType('eq')
                            ->create();
        
        // Add filters
        $this->searchCriteriaBuilder->addFilters([$urlKeyFilter]);
        $this->searchCriteriaBuilder->addFilters([$activeFilter]);
        $this->searchCriteriaBuilder->addFilters([$storeFilter]);
        
        // Build searchCriteria object with filters
        $searchCriteria = $this->searchCriteriaBuilder->create();        
        
        // Get active categories by url key
        return $this->getList($searchCriteria);        
    }
    
    /**
     * {@inheritdoc}
     */
    public function save(\Coke\Faq\Api\CategoryInterface $category)
    {
        // Category save
        $category->save();
        
        return $category;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(\Coke\Faq\Api\CategoryInterface $category)
    {
        $category->delete();
        
        return $category;
    }    
    
    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $searchResult = $this->searchResultsFactory->create();
        
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        // Add sort orders
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        
        foreach ($sortOrders as $sortOrder) {
            $collection->addOrder(
                $sortOrder->getField(),
                ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }      
        
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;        
    }
    
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Coke\Faq\Model\ResourceModel\Category\Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }    
}