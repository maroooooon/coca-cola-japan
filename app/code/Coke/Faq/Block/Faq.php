<?php

namespace Coke\Faq\Block;

class Faq
    extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_FAQ_PAGE_TITLE = 'coke_faq/faq_settings/title';
    const XML_PATH_FAQ_LIMIT_ITEMS_PER_CATEGORY = 'coke_faq/faq_settings/items_per_category';

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

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        $this->itemRepository = $itemRepository;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get Active FAQ Categories for current store
     *
     * @return []
     */
    public function getFaqCategories()
    {
        // Get active categories for current storeview
        $categoriesSearch = $this->categoryRepository->getActiveCategoriesForCurrentStore('ASC');

        $categories = [];
        foreach ($categoriesSearch->getItems() as $category) {
            $categories[$category->getId()]['name'] = $category->getName();
            $categories[$category->getId()]['url_key'] = $category->getUrlKey();
        }

        return $categories;
    }

    protected function _getFaqItemsByCategoryId($faqCategoryId, $limitItems = false, $currentItemId = null, $limitQty = 0)
    {
        $limit = 0;
        if ($limitItems) {
            $limit = $limitQty > 0 ? $limitQty : $this->_scopeConfig->getValue(self::XML_PATH_FAQ_LIMIT_ITEMS_PER_CATEGORY, 'store');
        }

        $itemsSearch = $this->itemRepository->getByCategoryId($faqCategoryId, 'ASC', $limitItems, $currentItemId, $limit);

        $items = [];
        foreach ($itemsSearch->getItems() as $item) {
            $items[$item->getId()]['title'] = $item->getTitle();
            $items[$item->getId()]['url_key'] = $item->getUrlKey();
        }

        return $items;
    }

    public function getFaqCategoriesAndItems()
    {
        // Get FAQ categories for current store
        $categories = $this->getFaqCategories();

        // Get FAQ categories items
        if (count($categories) > 0) {
            foreach ($categories as $categoryId => $category) {
                $categories[$categoryId]['items'] = $this->_getFaqItemsByCategoryId($categoryId, true);
            }
        }

        return $categories;
    }


    /**
     * Get FAQ main page title
     *
     * @return string
     */
    public function getFaqPageTitle()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_FAQ_PAGE_TITLE, 'store');
    }
}
