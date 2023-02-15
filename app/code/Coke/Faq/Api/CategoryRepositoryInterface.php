<?php

namespace Coke\Faq\Api;

interface CategoryRepositoryInterface
{
    /**
     * Save \Coke\Faq\Model\Category
     * 
     * @param \Coke\Faq\Api\CategoryInterface $category
     * @return \Coke\Faq\Api\CategoryInterface
     */
    public function save(\Coke\Faq\Api\CategoryInterface $category);
    
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
     * @param \Coke\Faq\Api\CategoryInterface $category
     * @return \Coke\Faq\Api\CategoryInterface
     */
    public function delete(\Coke\Faq\Api\CategoryInterface $category);
    
    /**
     * Get category by url key
     * 
     * @param string $urlKey
     * 
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getByUrlKey($urlKey);
    
    /**
     * Get active categories for the current store, sort by sort_order
     * 
     * @param string $order [ASC, DESC]
     * 
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getActiveCategoriesForCurrentStore($order);
    
    /**
     * Get active categories, sort by name
     * 
     * @param string $order [ASC, DESC]
     * 
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getActiveCategories($order);
    
    /**
     * Get category instance
     * 
     * @param integer $categoryId Category ID
     * 
     * @return \Coke\Faq\Model\Category
     */
    public function get($categoryId);
    
    /**
     * Create empty category instance
     * 
     * @return \Coke\Faq\Model\Category
     */
    public function create();
}
