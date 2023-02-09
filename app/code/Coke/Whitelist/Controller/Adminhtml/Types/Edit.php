<?php

namespace Coke\Whitelist\Controller\Adminhtml\Types;

use Coke\Whitelist\Api\Data\WhitelistTypeInterface;
use Coke\Whitelist\Api\Data\WhitelistTypeInterfaceFactory;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
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
    protected $whitelistTypeFactory;
    /**
     * @var WhitelistRepositoryInterface
     */
    protected $whitelistTypeRepository;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param Session $backendSession
     * @param Registry $registry
     * @param WhitelistTypeInterfaceFactory $whitelistTypeFactory
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        Session $backendSession,
        Registry $registry,
        WhitelistTypeInterfaceFactory $whitelistTypeFactory,
        WhitelistTypeRepositoryInterface $whitelistTypeRepository
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->backendSession = $backendSession;
        $this->registry = $registry;
        $this->whitelistTypeFactory = $whitelistTypeFactory;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
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

        /** @var WhitelistTypeInterface $whitelist */
        if ($id) {
            try {
                $whitelist = $this->whitelistTypeRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__("This type no longer exists"));
                return $this->_redirect('*/*/');
            }
        } else {
            $whitelist = $this->whitelistTypeFactory->create();
        }

        $data = $this->backendSession->getFormData(true);

        if (!empty($data)) {
            $whitelist->setData($data);
        }

        $this->registry->register('whitelist_type', $whitelist);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Whitelist Type') : __('New Whitelist Type'),
            $id ? __('Edit Whitelist Type') : __('New Whitelist Type')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Whitelist Types'));
        $resultPage->getConfig()->getTitle()
            ->prepend($whitelist->getId() ? $whitelist->getName() : __('New Whitelist Type'));

        return $resultPage;
    }
}
