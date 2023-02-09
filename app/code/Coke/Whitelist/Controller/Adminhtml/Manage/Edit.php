<?php

namespace Coke\Whitelist\Controller\Adminhtml\Manage;

use Coke\Whitelist\Api\Data\WhitelistInterface;
use Coke\Whitelist\Api\Data\WhitelistInterfaceFactory;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Coke_Whitelist::whitelist_create';

    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var Session
     */
    protected $backendSession;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var WhitelistInterfaceFactory
     */
    protected $whitelistFactory;
    /**
     * @var WhitelistRepositoryInterface
     */
    protected $whitelistRepository;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param Session $backendSession
     * @param Registry $registry
     * @param WhitelistInterfaceFactory $whitelistFactory
     * @param WhitelistRepositoryInterface $whitelistRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        Session $backendSession,
        Registry $registry,
        WhitelistInterfaceFactory $whitelistFactory,
        WhitelistRepositoryInterface $whitelistRepository
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->backendSession = $backendSession;
        $this->registry = $registry;
        $this->whitelistFactory = $whitelistFactory;
        $this->whitelistRepository = $whitelistRepository;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Coke_Whitelist::whitelist')
            ->addBreadcrumb(__('Whitelist'), __('Whitelist'))
            ->addBreadcrumb(__('Manage Whitelist'), __('Manage Whitelist'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var WhitelistInterface $whitelist */
        if ($id) {
            try {
                $whitelist = $this->whitelistRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__("This KML file no longer exists"));
                return $this->_redirect('*/*/');
            }
        } else {
            $whitelist = $this->whitelistFactory->create();
        }

        $data = $this->backendSession->getFormData(true);

        if (!empty($data)) {
            $whitelist->setData($data);
        }

        $this->registry->register('whitelist', $whitelist);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Whitelist Item') : __('New Whitelist Item'),
            $id ? __('Edit Whitelist Item') : __('New Whitelist Item')
        );
        if ($whitelist->getName()) {
            $resultPage->getConfig()->getTitle()
                ->prepend($whitelist->getId() ? $whitelist->getName() : __('New Whitelist Item'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Whitelist Items'));
        }

        return $resultPage;
    }
}
