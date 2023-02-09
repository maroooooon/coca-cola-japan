<?php


namespace Coke\FaqCustom\Block\Item;

class Show extends \Coke\Faq\Block\Item\Show
{
    /**
     * Show constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Coke\Faq\Api\ItemRepositoryInterface $itemRepository
     * @param \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $itemRepository, $categoryRepository,$data);
    }

    /**
     * @return array|\Coke\Faq\Block\Item\[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItem()
    {
        // Get URL Key from parameters
        $params = $this->getRequest()->getParams();

        $itemUrlKey = "";
        if (count($params) > 0) {
            reset($params);
            $itemUrlKey = key($params);
        }

        // Get item by URL Key
        $itemSearch = $this->itemRepository->getByUrlKey($itemUrlKey);
        $storeId = $this->_storeManager->getStore()->getId();
        $item = [];
        foreach ($itemSearch->getItems() as $i) {
            $currentCategory = $this->categoryRepository->get($i->getFaqCategoryId());
            if($storeId == $currentCategory->getStoreId()) {
                $item['id'] = $i->getId();
                $item['title'] = $i->getTitle();
                $item['category_id'] = $i->getFaqCategoryId();
                $item['description'] = $i->getDescription();
            }
        }

        return $item;
    }
}