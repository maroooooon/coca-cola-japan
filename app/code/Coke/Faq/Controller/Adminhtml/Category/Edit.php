<?php

namespace Coke\Faq\Controller\Adminhtml\Category;

class Edit
    extends \Coke\Faq\Controller\Adminhtml\Category
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
     * @var \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    protected $sessionFactory;
    
    /**
     * @var \Coke\Faq\Api\CategoryRepositoryInterface 
     */
    protected $categoryRepository;
    
    /**
     * Constructor
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,    
        \Magento\Backend\Model\SessionFactory $sessionFactory
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryRepository = $categoryRepository;
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
        
        // Validate ID
        if ($id) {
            $category = $this->categoryRepository->get($id);
            if (!$category->getId()) {
                $this->messageManager->addError(__('This FAQ Category no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $category = $this->categoryRepository->create();
        }
        
        // Set entered data if was error when we do save
        $data = $this->sessionFactory->create()->getFormData(true);
        if (!empty($data)) {
            $category->setName($data["name"]);
            $category->setIsActive($data["is_active"]);
            $category->setSortOrder($data["sort_order"]);
            $category->setUrlKey($data["url_key"]);
            $category->setStoreId($data["store_id"]);
        }

        // Register model to use later in blocks
        $this->_coreRegistry->register('faqcategory_model', $category);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        // 5. Build edit form
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit FAQ Category') : __('New FAQ Category'),
            $id ? __('Edit FAQ Category') : __('New FAQ Category')
        );
        
        $resultPage->getConfig()->getTitle()->prepend(
            $category->getId() ? $category->getName() : __('New FAQ Category')
        );

        return $resultPage;
    }
}