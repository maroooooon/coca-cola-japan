<?php

namespace Coke\Whitelist\Api;

use Coke\Whitelist\Api\Data\WhitelistOrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchResultsInterface;

interface WhitelistOrderRepositoryInterface
{
    /**
     * @param int $id
     * @return WhitelistOrderInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param WhitelistOrderInterface $whitelistOrder
     * @return WhitelistOrderInterface
     * @throws AlreadyExistsException
     * @throws \Exception
     */
    public function save(WhitelistOrderInterface $whitelistOrder);

    /**
     * @param WhitelistOrderInterface $whitelistOrder
     * @return bool
     * @throws \Exception
     */
    public function delete(WhitelistOrderInterface $whitelistOrder);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @return array whitelistorderid
     */
    public function getWhitelistOrderId();
}
