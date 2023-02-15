<?php

namespace Coke\Faq\Model;

class ItemRepository
    implements \Coke\Faq\Api\ItemRepositoryInterface
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
     * @var \Coke\Faq\Model\ItemFactory
     */
    protected $itemFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrder $sortOrder,
        \Magento\Framework\Api\SearchResultsFactory $searchResultsFactory,
        \Coke\Faq\Model\ItemFactory $itemFactory,
        \Coke\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory
    ){
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrder = $sortOrder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $item = $this->itemFactory->create();

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $item = $this->itemFactory->create()
                                  ->load($id);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCategoryId($categoryId, $order = false, $limitItems = false, $currentItemId = null, $limitQty = 5)
    {
        // Category filter
        $categoryFilter = $this->filterBuilder
                               ->setField('faq_category_id')
                               ->setValue($categoryId)
                               ->setConditionType('eq')
                               ->create();

        // Active filter
        $activeFilter = $this->filterBuilder
                             ->setField('is_active')
                             ->setValue(1)
                             ->setConditionType('eq')
                             ->create();

        // Active filter
        if (!is_null($currentItemId)){
            $itemFilter = $this->filterBuilder
                             ->setField('entity_id')
                             ->setValue($currentItemId)
                             ->setConditionType('neq')
                             ->create();
            $this->searchCriteriaBuilder->addFilters([$itemFilter]);
        }


        // Add filter
        $this->searchCriteriaBuilder->addFilters([$categoryFilter]);
        $this->searchCriteriaBuilder->addFilters([$activeFilter]);

        // Validate order
        if (($order !== false) && (in_array(strtoupper($order), $this->validOrders))) {
            // Add sort order
            $orderCriteria = $this->sortOrder->setField('sort_order')
                                             ->setDirection($order);

            $this->searchCriteriaBuilder->addSortOrder($orderCriteria);
        }

        // Set page
        if ($limitItems) {
            $this->searchCriteriaBuilder->setPageSize($limitQty);
            $this->searchCriteriaBuilder->setCurrentPage(1);
        }

        // Build searchCriteria object with filters
        $searchCriteria = $this->searchCriteriaBuilder->create();

        // Get passwords history for current user
        return $this->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUrlKey($urlKey)
    {
        // Build SearchCriteria object to get item by url key
        $itemFilter = $this->filterBuilder
                           ->setField('url_key')
                           ->setValue($urlKey)
                           ->setConditionType('eq')
                           ->create();

        $this->searchCriteriaBuilder->addFilters([$itemFilter]);

        // Build searchCriteria object with filters
        $searchCriteria = $this->searchCriteriaBuilder->create();

        // Get items by url key
        return $this->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Coke\Faq\Api\ItemInterface $item)
    {
        // Save item
        $item->save();

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Coke\Faq\Api\ItemInterface $item)
    {
        // Delete item
        $item->delete();

        return $item;
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

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

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
        \Coke\Faq\Model\ResourceModel\Item\Collection $collection
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