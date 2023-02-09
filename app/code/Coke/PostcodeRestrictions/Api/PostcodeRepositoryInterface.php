<?php

namespace Coke\PostcodeRestrictions\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Coke\PostcodeRestrictions\Api\Data\PostcodeInterface;

interface PostcodeRepositoryInterface
{
    /**
     * @param int $id
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param string $postcode
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByPostcode(string $postcode);

    /**
     * @param \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface $postcode
     * @return \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface
     * @throws \Exception
     */
    public function save(PostcodeInterface $postcode);

    /**
     * @param \Coke\PostcodeRestrictions\Api\Data\PostcodeInterface $postcode
     * @return bool
     * @throws \Exception
     */
    public function delete(PostcodeInterface $postcode);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Coke\Whitelist\Api\Data\WhitelistSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
