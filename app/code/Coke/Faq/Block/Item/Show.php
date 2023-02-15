<?php

namespace Coke\Faq\Block\Item;

class Show
    extends \Coke\Faq\Block\Faq
{
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

        parent::__construct(
            $context,
            $itemRepository,
            $categoryRepository,
            $data
        );
    }

    /**
     * Get item information by URL Key
     *
     * @return \Coke\Faq\Block\Item\[]
     */
    public function getItem()
    {
        // Get URL Key from parameters
        $params = $this->getRequest()->getParams();

        if (count($params) > 0) {
            reset($params);
            $itemUrlKey = key($params);
        }

        // Get item by URL Key
        $itemSearch = $this->itemRepository->getByUrlKey($itemUrlKey);

        $item = [];
        foreach ($itemSearch->getItems() as $i) {
            $item['id'] = $i->getId();
            $item['title'] = $i->getTitle();
            $item['category_id'] = $i->getFaqCategoryId();
            $item['description'] = $i->getDescription();
        }

        return $item;
    }

    /**
     * Get category
     *
     * @param integer $categoryId Category ID
     *
     * @return \Coke\Faq\Model\Category
     */
    public function getCategory($categoryId)
    {
        return $this->categoryRepository->get($categoryId);
    }

    /**
     * Get all items by category Id
     *
     * @return []
     */

    public function getItemsByCategoryId($categoryId, $currentItemId)
    {
        $items = $this->_getFaqItemsByCategoryId($categoryId, false, $currentItemId);

        return $items;
    }
}
