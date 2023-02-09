<?php

namespace Coke\PostcodeRestrictions\Model;

use Coke\PostcodeRestrictions\Api\Data\PostcodeInterface;
use Coke\PostcodeRestrictions\Api\Data\PostcodeSearchResultInterface;
use Coke\PostcodeRestrictions\Api\PostcodeRepositoryInterface;
use Coke\PostcodeRestrictions\Model\ResourceModel\Postcode\Collection;
use Coke\PostcodeRestrictions\Model\ResourceModel\Postcode\CollectionFactory as PostcodeCollectionFactory;
use Coke\PostcodeRestrictions\Api\Data\PostcodeSearchResultInterfaceFactory as PostcodeSearchResultFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;

class PostcodeRepository implements PostcodeRepositoryInterface
{
    /**
     * @var \Coke\PostcodeRestrictions\Model\PostcodeFactory
     */
    private $postcodeFactory;
    /**
     * @var PostcodeCollectionFactory
     */
    private $postcodeCollectionFactory;
    /**
     * @var PostcodeSearchResultFactory
     */
    private $postcodeSearchResultFactory;

    /**
     * WhitelistRepository constructor.
     * @param \Coke\PostcodeRestrictions\Model\PostcodeFactory $postcodeFactory
     * @param PostcodeCollectionFactory $postcodeCollectionFactory
     * @param PostcodeSearchResultFactory $postcodeSearchResultFactory
     */
    public function __construct(
        PostcodeFactory $postcodeFactory,
        PostcodeCollectionFactory $postcodeCollectionFactory,
        PostcodeSearchResultFactory $postcodeSearchResultFactory
    ) {
        $this->postcodeFactory = $postcodeFactory;
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
        $this->postcodeSearchResultFactory = $postcodeSearchResultFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return PostcodeSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->postcodeCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * @param $value
     * @param $field
     * @return Postcode
     * @throws NoSuchEntityException
     */
    public function getBy($value, $field = null)
    {
        /** @var Postcode $postcode */
        $postcode = $this->postcodeFactory->create();
        $postcode->getResource()->load($postcode, $value, $field);

        if (!$postcode->getId()) {
            throw new NoSuchEntityException(__('Unable to find postcode restriction with %1 "%2"', $field ?: 'ID', $value));
        }

        return $postcode;
    }

    /**
     * @param int $id
     * @return PostcodeInterface|Postcode
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        return $this->getBy($id);
    }

    /**
     * @param PostcodeInterface $postcode
     * @return PostcodeInterface|Postcode
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(PostcodeInterface $postcode)
    {
        /** @var Postcode $postcode */
        $postcode->getResource()->save($postcode);
        return $postcode;
    }

    /**
     * @param PostcodeInterface $postcode
     * @return bool
     * @throws \Exception
     */
    public function delete(PostcodeInterface $postcode)
    {
        /** @var Postcode $postcode */
        $postcode->getResource()->delete($postcode);
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
     * @param string $postcode
     * @return PostcodeInterface
     * @throws NoSuchEntityException
     */
    public function getByPostcode(string $postcode): PostcodeInterface
    {
        return $this->getBy($postcode, 'postcode');
    }
}
