<?php

namespace Coke\Whitelist\Model;

use Coke\Whitelist\Api\Data\WhitelistInterface;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Coke\Whitelist\Exception\WhitelistEntityContainsIllegalCharacterException;
use Coke\Whitelist\Exception\WhitelistEntityDeniedException;
use Coke\Whitelist\Exception\WhitelistEntityNotFoundException;
use Coke\Whitelist\Model\ResourceModel\Whitelist\Collection;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory as WhitelistCollectionFactory;
use Coke\Whitelist\Api\Data\WhitelistSearchResultInterfaceFactory as WhitelistSearchResultFactory;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

class WhitelistRepository implements WhitelistRepositoryInterface
{

    /**
     * @var \Coke\Whitelist\Model\WhitelistFactory
     */
    private $whitelistFactory;
    /**
     * @var WhitelistCollectionFactory
     */
    private $whitelistCollectionFactory;
    /**
     * @var WhitelistSearchResultFactory
     */
    private $whitelistSearchResultFactory;
    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;
    /**
     * @var ModuleConfig
     */
    private $config;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * WhitelistRepository constructor.
     * @param \Coke\Whitelist\Model\WhitelistFactory $whitelistFactory
     * @param WhitelistCollectionFactory $whitelistCollectionFactory
     * @param WhitelistSearchResultFactory $whitelistSearchResultFactory
     */
    public function __construct(
        WhitelistFactory $whitelistFactory,
        WhitelistCollectionFactory $whitelistCollectionFactory,
        WhitelistSearchResultFactory $whitelistSearchResultFactory,
        WhitelistTypeRepositoryInterface $whitelistTypeRepository,
        ModuleConfig $config,
        UrlInterface $url
    ) {
        $this->whitelistFactory = $whitelistFactory;
        $this->whitelistCollectionFactory = $whitelistCollectionFactory;
        $this->whitelistSearchResultFactory = $whitelistSearchResultFactory;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Coke\Whitelist\Api\Data\WhitelistSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->whitelistCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * @param $value
     * @param $field
     * @return Whitelist
     * @throws NoSuchEntityException
     */
    public function getBy($value, $field = null)
    {
        /** @var Whitelist $whitelist */
        $whitelist = $this->whitelistFactory->create();
        $whitelist->getResource()->load($whitelist, $value, $field);

        if (!$whitelist->getId()) {
            throw new NoSuchEntityException(__('Unable to find white list name with %1 "%2"', $field ?: 'ID', $value));
        }

        return $whitelist;
    }

    /**
     * @param int $id
     * @return WhitelistInterface|Whitelist
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        return $this->getBy($id);
    }

    /**
     * @param WhitelistInterface $whitelist
     * @return WhitelistInterface|Whitelist
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(WhitelistInterface $whitelist)
    {
        /** @var Whitelist $whitelist */
        $whitelist->getResource()->save($whitelist);
        return $whitelist;
    }

    /**
     * @param WhitelistInterface $whitelist
     * @return bool
     * @throws \Exception
     */
    public function delete(WhitelistInterface $whitelist)
    {
        /** @var Whitelist $whitelist */
        $whitelist->getResource()->delete($whitelist);
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
        $searchResults = $this->whitelistSearchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function getByValue($name, $storeId)
    {
        $name = str_replace(PHP_EOL, '%', $name);
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id', $storeId)
            ->addFieldToFilter('value', ['like' => $name]);

        $firstItem = $collection->load()->getFirstItem();
        if (!$firstItem->getId()) {
            throw new NoSuchEntityException(__('Provided Name isn\'t approved'));
        }

        return $firstItem;
    }


    /**
     * @param $name
     * @param $storeId
     */
    public function isDeniedValue($value, $storeId, $typeId)
    {
        $value = str_replace(PHP_EOL, '%', $value);
        $whitelistType = $this->whitelistTypeRepository->getById($typeId);
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFilter('status', WhitelistStatus::DENIED)
            ->addFilter('store_id', $storeId)
            ->addFilter('type_id', $typeId)
            ->addFieldToFilter('value', ['like' => $value]);

        if ($collection->load()->getFirstItem()->getId()) {
            throw new WhitelistEntityDeniedException(__('The %1 you entered was rejected.', $whitelistType->getName()));
        }
    }

    public function isValueApproved($typeId, $name, $storeId): string
    {
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id', $storeId)
            ->addFilter('type_id', $typeId)
            ->addFieldToFilter('value', ['eq' => $name]);

        $item = $collection->load()->getFirstItem();
        if ($item->getId()) {
            return $item->getData('value');
        }

        return false;
    }

    public function isValueSortaApproved($name, $storeId, $typeId): string
    {
        $name = str_replace(PHP_EOL, '%', $name);
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id', $storeId)
            ->addFilter('type_id', $typeId)
            ->addFieldToFilter('value', ['like' => $name]);

        $item = $collection->load()->getFirstItem();
        if ($item->getId()) {
            return $name;
        }

        throw new WhitelistEntityNotFoundException(__('Sorry! That\'s not a valid option.'));
    }

    /**
     * @param $value
     * @param $typeId
     *
     * @return void
     * @throws WhitelistEntityContainsIllegalCharacterException
     */
    public function containsIllegalCharacter($value, $typeId)
    {
        if ($offendingCharacter = $this->hasAnyIllegalCharacter($value, mb_str_split($this->config->getIllegalCharacters()))) {
            throw new WhitelistEntityContainsIllegalCharacterException(__('The symbol you entered is not allowed'));
        }
    }

    /**
     * @param string $haystack
     * @param array  $needle
     * @param bool   $outputChr
     *
     * @return false|int|mixed
     */
    protected function hasAnyIllegalCharacter(string $haystack, array $needle, bool $outputChr = true)
    {
        foreach ($needle as $chr) {
            if (($pos = mb_strpos($haystack, $chr)) !== false) {
                $output = $chr;

                if (!$outputChr) {
                    $output = $pos;
                }

                return $output;
            }
        }

        return false;
    }
}
