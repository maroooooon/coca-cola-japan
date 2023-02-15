<?php

namespace Coke\Faq\Controller\Adminhtml\Item;

class Edit
    extends \Coke\Faq\Controller\Adminhtml\Item
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Coke\Faq\Api\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Coke\Faq\Api\ItemRepositoryInterface $itemRepository
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository,
        \Magento\Backend\Model\SessionFactory $sessionFactory
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->itemRepository = $itemRepository;
        $this->sessionFactory = $sessionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Get ID from parameters
        $id = $this->getRequest()->getParam('id');

        // Check if edition or creation
        if ($id) {
            // Edition
            $item = $this->itemRepository->get($id);
            if (!$item->getEntityId()) {
                // Edition but item does not exists
                $this->messageManager->addError(__('This FAQ Item no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        } else {
            // Creation
            $item = $this->itemRepository->create();
        }

        // Set entered data if was error when we do save
        $data = $this->sessionFactory->create()->getFormData(true);
        if (!empty($data)) {
            $item->setTitle($data["title"]);
            $item->setIsActive($data["is_active"]);
            $item->setUrlKey($data["url_key"]);
            $item->setFaqCategoryId($data["faq_category_id"]);
            //TODO: Hidden for future developments
//            $item->setTags($data["tags"]);
//            $item->setMostFrequently($data["most_frequently"]);
            $item->setSortOrder($data["sort_order"]);
            $item->setDescription($data["description"]);
        }

        // Register model to use later in blocks
        $this->_coreRegistry->register('faqitem_model', $item);

        // Create result page instance
        $resultPage = $this->resultPageFactory->create();

        // 5. Build edit form
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit FAQ Item') : __('New FAQ Item'),
            $id ? __('Edit FAQ Item') : __('New FAQ Item')
        );

        $resultPage->getConfig()->getTitle()->prepend($item->getEntityId() ? $item->getTitle() : __('New FAQ Item'));

        return $resultPage;
    }
}