<?php

namespace Coke\Faq\Controller\Adminhtml\Item;

class Delete
    extends \Coke\Faq\Controller\Adminhtml\Item
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;    
    
    /**
     * @var \Coke\Faq\Api\ItemRepositoryInterface 
     */
    protected $itemRepository;
    
    /**
     * Constructor
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Coke\Faq\Api\ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository
    ){
        $this->itemRepository = $itemRepository;
        parent::__construct($context, $coreRegistry);
    }    
    
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Create result redirect instance
        $resultRedirect = $this->resultRedirectFactory->create();
        
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $item = $this->itemRepository->get($id);
                $title = $item->getTitle();
                $this->itemRepository->delete($item);
                
                // display success message
                $this->messageManager->addSuccess(__('You deleted the FAQ Item: ') . $title);

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
        $this->messageManager->addError(__('Can not find a FAQ Item to delete.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
