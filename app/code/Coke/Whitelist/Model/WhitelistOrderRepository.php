<?php

namespace Coke\Whitelist\Model;

use Coke\Whitelist\Api\Data\WhitelistOrderInterface;
use Coke\Whitelist\Api\Data\WhitelistOrderInterfaceFactory;
use Coke\Whitelist\Api\WhitelistOrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Coke\Whitelist\Model\WhitelistOrderFactory;
use Coke\Whitelist\Model\ResourceModel\WhitelistOrder as WhitelistOrderResource;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Coke\Whitelist\Model\ResourceModel\WhitelistOrder\Collection;
use Coke\Whitelist\Model\ResourceModel\WhitelistOrder\CollectionFactory as WhitelistOrderCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;

class WhitelistOrderRepository implements WhitelistOrderRepositoryInterface
{
    private $resource;

    private $searchResultsFactory;

    private $whitelistOrderCollectionFactory;

    private $dataWhitelistOrderFactory;

    private $dataObjectHelper;

    public function __construct(
        WhitelistOrderResource $resource,
        SearchResultsInterfaceFactory $searchResultsFactory,
        WhitelistOrderCollectionFactory $whitelistOrderCollectionFactory,
        WhitelistOrderInterfaceFactory $whitelistOrderFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->whitelistOrderCollectionFactory = $whitelistOrderCollectionFactory;
        $this->dataWhitelistOrderFactory = $whitelistOrderFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param int $id
     * @return WhitelistOrderInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        return $this->getBy($id);
    }

    /**
     * @param WhitelistOrderInterface $whitelistOrder
     * @return WhitelistOrderInterface
     * @throws AlreadyExistsException
     * @throws \Exception
     */
    public function save(WhitelistOrderInterface $whitelistOrder)
    {
        $this->resource->save($whitelistOrder);
        return $whitelistOrder;
    }

    /**
     * @param WhitelistOrderInterface $whitelistOrder
     * @return bool
     * @throws \Exception
     */
    public function delete(WhitelistOrderInterface $whitelistOrder)
    {
        $this->resource->delete($whitelistOrder);
        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->whitelistOrderCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $whitelistOrders = [];

        foreach ($collection as $item) {
            $dataWhitelistOrder = $this->dataWhitelistOrderFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $dataWhitelistOrder,
                $item->getData(),
                WhitelistOrderInterface::class
            );
            $whitelistOrders[] = $dataWhitelistOrder;
        }

        $searchResults->setItems($whitelistOrders);

        return $searchResults;
    }

    /**
     * @param $value
     * @param null $field
     * @return WhitelistOrderInterface
     * @throws NoSuchEntityException
     */
    public function getBy($value, $field = null)
    {
        /** @var WhitelistOrder $whitelist */
        $whitelistOrder = $this->WhitelistOrderFactory->create();
        $whitelistOrder->getResource()->load($whitelistOrder, $value, $field);

        if (!$whitelistOrder->getId()) {
            throw new NoSuchEntityException(__('Unable to find whitelist order record with %1 "%2"', $field ?: 'ID', $value));
        }

        return $whitelistOrder;
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
     * @return array
     */
    public function getWhitelistOrderId()
    {
        $collection = $this->whitelistOrderCollectionFactory->create();
        $whitelistOrders = [];
        foreach ($collection as $item) {
            $whitelistOrders[] = $item->getOrderId();
        }
        return $whitelistOrders;
    }
}
