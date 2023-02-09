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

class Save extends Action
{
    const ADMIN_RESOURCE = 'Coke_Whitelist::whitelist_create';

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
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        /** @var WhitelistInterface $model */
        $model = $this->whitelistFactory->create();
        $model->setData($data);

        $this->_eventManager->dispatch(
            'coke_whitelist_whitelist_prepare_save',
            ['event' => $model, 'request' => $this->getRequest()]
        );

        try {
            $this->whitelistRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the whitelist item.'));
            $this->backendSession->setFormData(false);

            if (!$model->isObjectNew()) {
                switch ($model->getStatus()) {
                    case WhitelistStatus::APPROVED:
                        if (!$this->whitelistService->updateOrdersForWhitelistForApproved($model->getId())) {
                            $this->messageManager->addErrorMessage(__('There was a problem updating related orders after approval. Check log.'));
                        }
                        break;
                    case WhitelistStatus::DENIED:
                        if (!$this->whitelistService->updateOrdersForWhitelistForDenied($model->getId())) {
                            $this->messageManager->addErrorMessage(__('There was a problem updating related orders after denied. Check log.'));
                        }
                        break;
                }
            }

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', [
                    'id' => $model->getId(),
                    '_current' => true
                ]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the whitelist item.'));
        }

        $this->_getSession()->setFormData($data);
        return $resultRedirect->setPath('*/*/edit', [
            'whitelist_id' => $this->getRequest()->getParam('whitelist_id')
        ]);
    }
}
