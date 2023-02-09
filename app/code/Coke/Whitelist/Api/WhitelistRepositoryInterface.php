<?php

namespace Coke\Whitelist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Coke\Whitelist\Api\Data\WhitelistInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface WhitelistRepositoryInterface
{

    /**
     * @param int $id
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param string $name
     * @param int $storeId
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     * @throws NoSuchEntityException
     */
    public function getByValue($name, $storeId);

    /**
     * @param \Coke\Whitelist\Api\Data\WhitelistInterface $whitelist
     * @return \Coke\Whitelist\Api\Data\WhitelistInterface
     * @throws \Exception
     */
    public function save(WhitelistInterface $whitelist);

    /**
     * @param \Coke\Whitelist\Api\Data\WhitelistInterface $whitelist
     * @return bool
     * @throws \Exception
     */
    public function delete(WhitelistInterface $whitelist);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Coke\Whitelist\Api\Data\WhitelistSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
