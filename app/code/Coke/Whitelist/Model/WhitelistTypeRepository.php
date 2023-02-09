<?php

namespace Coke\Whitelist\Model;

use Coke\Whitelist\Api\Data\WhitelistInterface;
use Coke\Whitelist\Api\Data\WhitelistTypeInterface;
use Coke\Whitelist\Api\Data\WhitelistTypeSearchResultInterface;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Coke\Whitelist\Model\ResourceModel\WhitelistType\Collection;
use Coke\Whitelist\Model\ResourceModel\WhitelistType\CollectionFactory as WhitelistTypeCollectionFactory;
use Coke\Whitelist\Api\Data\WhitelistTypeSearchResultInterfaceFactory as WhitelistTypeSearchResultFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;

class WhitelistTypeRepository implements WhitelistTypeRepositoryInterface
{
    /**
     * @var \Coke\Whitelist\Model\WhitelistTypeFactory
     */
    private $whitelistTypeFactory;
    /**
     * @var WhitelistTypeCollectionFactory
     */
    private $whitelistTypeCollectionFactory;
    /**
     * @var WhitelistTypeSearchResultFactory
     */
    private $whitelistTypeSearchResultFactory;

    /**
     * WhitelistRepository constructor.
     * @param \Coke\Whitelist\Model\WhitelistTypeFactory $whitelistTypeFactory
     * @param WhitelistTypeCollectionFactory $whitelistTypeCollectionFactory
     * @param WhitelistTypeSearchResultFactory $whitelistTypeSearchResultFactory
     */
    public function __construct(
        WhitelistTypeFactory $whitelistTypeFactory,
        WhitelistTypeCollectionFactory $whitelistTypeCollectionFactory,
        WhitelistTypeSearchResultFactory $whitelistTypeSearchResultFactory
    ) {
        $this->whitelistTypeFactory = $whitelistTypeFactory;
        $this->whitelistTypeCollectionFactory = $whitelistTypeCollectionFactory;
        $this->whitelistTypeSearchResultFactory = $whitelistTypeSearchResultFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return WhitelistTypeSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->whitelistTypeCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * @param $value
     * @param $field
     * @return WhitelistType
     * @throws NoSuchEntityException
     */
    public function getBy($value, $field = null)
    {
        /** @var WhitelistType $whitelistType */
        $whitelistType = $this->whitelistTypeFactory->create();
        $whitelistType->getResource()->load($whitelistType, $value, $field);

        if (!$whitelistType->getId()) {
            throw new NoSuchEntityException(__('Unable to find whitelist type with %1 "%2"', $field ?: 'ID', $value));
        }

        return $whitelistType;
    }

    /**
     * @param int $id
     * @return WhitelistTypeInterface|WhitelistType
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        return $this->getBy($id);
    }

    /**
     * @param WhitelistTypeInterface $whitelistType
     * @return WhitelistInterface|Whitelist
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(WhitelistTypeInterface $whitelistType)
    {
        /** @var Whitelist $whitelistType */
        $whitelistType->getResource()->save($whitelistType);
        return $whitelistType;
    }

    /**
     * @param WhitelistTypeInterface $whitelistType
     * @return bool
     * @throws \Exception
     */
    public function delete(WhitelistTypeInterface $whitelistType)
    {
        /** @var Whitelist $whitelistType */
        $whitelistType->getResource()->delete($whitelistType);
        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $collection
     * @return mixed
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $searchResults = $this->whitelistTypeSearchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @param string $name
     * @return WhitelistTypeInterface|WhitelistType
     * @throws NoSuchEntityException
     */
    public function getByName($name)
    {
        return $this->getBy($name, 'name');
    }
}
