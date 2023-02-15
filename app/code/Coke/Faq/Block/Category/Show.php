<?php

namespace Coke\Faq\Block\Category;
 
class Show 
    extends \Coke\Faq\Block\Faq
{

    protected $categoryName = '';
    
    /**
     *
     * @var \Coke\Faq\Model\ItemRepository
     */
    protected $itemRepository;
    
    /**
     *
     * @var \Coke\Faq\Model\CategoryRepository 
     */
    protected $repositoryRepository;    
    
    /**
     * Constructor
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Coke\Faq\Api\ItemRepositoryInterface $itemRepository
     * @param \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        $this->itemRepository = $itemRepository;
        $this->categoryRepository = $categoryRepository;        
        
        parent::__construct(
            $context, 
            $itemRepository,
            $categoryRepository,    
            $data
        );
    }    
    
    /**
     * Get FAQ Categories for current store
     * 
     * @return []
     */
    protected function _getFaqCategoryByUrlKey($categoryUrlKey)
    {
        // Get category by URL Key
        $categorySearch = $this->categoryRepository->getByUrlKey($categoryUrlKey);
        
        $category = [];
        foreach ($categorySearch->getItems() as $value) {
            $category[$value->getId()]['name'] = $value->getName();
            $category[$value->getId()]['url_key'] = $value->getUrlKey();
        }
        
        return $category;
    }
    
    public function getCategory()
    {
        $params = $this->getRequest()->getParams();
        
        if (count($params) > 0) {
            reset($params);
            $categoryUrlKey = key($params);
        }
        
        // Get categories
        $category = $this->_getFaqCategoryByUrlKey($categoryUrlKey);
        
        // Get FAQ categories items
        if (count($category) > 0) {
            foreach ($category as $categoryId => $value) {
                $this->categoryName = $value['name'];
                $category[$categoryId]['items'] = $this->_getFaqItemsByCategoryId($categoryId, false);
            }
        }
        
        return $category;
    }

    public function getCategoryName()
    {
        return $this->categoryName;
    }
}
