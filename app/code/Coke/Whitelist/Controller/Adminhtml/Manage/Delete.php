<?php

namespace Coke\Whitelist\Controller\Adminhtml\Manage;

use Coke\Whitelist\Api\Data\WhitelistInterface;
use Coke\Whitelist\Api\Data\WhitelistInterfaceFactory;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Coke\Whitelist\Api\WhitelistManagementInterface;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Coke_Whitelist::whitelist_delete';

    /**
     * @var Session
     */
    private $backendSession;
    /**
     * @var WhitelistInterfaceFactory
     */
    private $whitelistFactory;
    /**
     * @var WhitelistRepositoryInterface
     */
    private $whitelistRepository;

    private $whitelistService;

    /**
     * Save constructor.
     * @param Context $context
     * @param Session $backendSession
     * @param WhitelistInterfaceFactory $whitelistFactory
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param WhitelistManagementInterface $whitelistService
     */
    public function __construct(
        Context $context,
        Session $backendSession,
        WhitelistInterfaceFactory $whitelistFactory,
        WhitelistRepositoryInterface $whitelistRepository,
        WhitelistManagementInterface $whitelistService
    ) {
        parent::__construct($context);
        $this->backendSession = $backendSession;
        $this->whitelistFactory = $whitelistFactory;
        $this->whitelistRepository = $whitelistRepository;
        $this->whitelistService = $whitelistService;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('id');
        $whitelistItem = $this->whitelistRepository->getById($id);

        if ($id) {
            try {
               $this->whitelistRepository->delete($whitelistItem);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the whitelist item.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find the whitelist item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
