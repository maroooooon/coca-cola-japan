<?php

namespace Coke\Faq\Api;

interface ItemRepositoryInterface
{
    /**
     * Save \Coke\Faq\Api\ItemInterface
     * 
     * @param \Coke\Faq\Api\ItemInterface $item
     * @return \Coke\Faq\Api\ItemInterface
     */
    public function save(\Coke\Faq\Api\ItemInterface $item);
    
    /**
     * Get list
     * 
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Delete
     * 
     * @param \Coke\Faq\Api\ItemInterface $item
     * @return \Coke\Faq\Api\ItemInterface
     */
    public function delete(\Coke\Faq\Api\ItemInterface $item);
    
    /**
     * Get category's item, with order and pagination
     * 
     * @param integer $categoryId
     * @param string $order
     * @param boolean $limitItems
     * @param integer $limitQty
     * 
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getByCategoryId($categoryId, $order = false, $limitItems = false, $limitQty = 5);
    
    /**
     * Get item by url key
     * 
     * @param string $urlKey
     * 
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getByUrlKey($urlKey);
    
    /**
     * Get item
     * 
     * @param integer $id
     * 
     * @return \Coke\Faq\Api\ItemInterface
     */
    public function get($id);

    /**
     * Create item
     * 
     * @return \Coke\Faq\Api\ItemInterface
     */
    public function create();
}
