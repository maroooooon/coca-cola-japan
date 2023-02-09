<?php

namespace Coke\Whitelist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Coke\Whitelist\Api\Data\WhitelistTypeInterface;

interface WhitelistTypeRepositoryInterface
{
    /**
     * @param int $id
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param string $name
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByName($name);

    /**
     * @param \Coke\Whitelist\Api\Data\WhitelistTypeInterface $whitelist
     * @return \Coke\Whitelist\Api\Data\WhitelistTypeInterface
     * @throws \Exception
     */
    public function save(WhitelistTypeInterface $whitelist);

    /**
     * @param \Coke\Whitelist\Api\Data\WhitelistTypeInterface $whitelist
     * @return bool
     * @throws \Exception
     */
    public function delete(WhitelistTypeInterface $whitelist);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Coke\Whitelist\Api\Data\WhitelistSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
