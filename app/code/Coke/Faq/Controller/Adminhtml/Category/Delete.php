<?php

namespace Coke\Faq\Controller\Adminhtml\Category;

class Delete
    extends \Coke\Faq\Controller\Adminhtml\Category
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;    
    
    /**
     * @var \Coke\Faq\Api\CategoryRepositoryInterface 
     */
    protected $categoryRepository;    
    
    /**
     * Constructor
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository
    ){
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $coreRegistry);
    }    
    
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Init result redirect instance
        $resultRedirect = $this->resultRedirectFactory->create();
        
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $category = $this->categoryRepository->get($id);
                $name = $category->getName();
                $this->categoryRepository->delete($category);
                
                // display success message
                $this->messageManager->addSuccess(__('You deleted the FAQ Category: ') . $name);

                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());

                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        
        // display error message
        $this->messageManager->addError(__('Can not find a FAQ Category to delete.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
