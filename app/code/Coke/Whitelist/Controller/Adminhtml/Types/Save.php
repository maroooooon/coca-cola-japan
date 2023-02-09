<?php

namespace Coke\Whitelist\Controller\Adminhtml\Types;

use Coke\Whitelist\Api\Data\WhitelistTypeInterface;
use Coke\Whitelist\Api\Data\WhitelistTypeInterfaceFactory;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Coke_Whitelist::whitelist_create';

    /**
     * @var Session
     */
    private $backendSession;
    /**
     * @var WhitelistTypeInterfaceFactory
     */
    private $whitelistTypeFactory;
    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param Session $backendSession
     * @param WhitelistTypeInterfaceFactory $whitelistTypeFactory
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     */
    public function __construct(
        Context $context,
        Session $backendSession,
        WhitelistTypeInterfaceFactory $whitelistTypeFactory,
        WhitelistTypeRepositoryInterface $whitelistTypeRepository
    ) {
        parent::__construct($context);
        $this->backendSession = $backendSession;
        $this->whitelistTypeFactory = $whitelistTypeFactory;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
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

        /** @var WhitelistTypeInterface $model */
        $model = $this->whitelistTypeFactory->create();
        $model->setData($data);

        $this->_eventManager->dispatch(
            'coke_whitelist_types_prepare_save',
            ['event' => $model, 'request' => $this->getRequest()]
        );

        try {
            $this->whitelistTypeRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the whitelist type.'));
            $this->backendSession->setFormData(false);

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
            $this->messageManager->addException($e, __('Something went wrong while saving the whitelist type.'));
        }

        $this->_getSession()->setFormData($data);
        return $resultRedirect->setPath('*/*/edit', [
            'whitelist_id' => $this->getRequest()->getParam('whitelist_id')
        ]);
    }
}
