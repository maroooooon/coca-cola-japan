<?php

namespace Coke\Whitelist\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Coke\Whitelist\Api\WhitelistManagementInterface;
use Magento\Framework\Controller\ResultFactory;

class MassApprove extends Action
{
    const ADMIN_RESOURCE = 'Coke_Whitelist::whitelist_approve';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var WhitelistManagementInterface
     */
    private $whitelistService;

    /**
     * MassApprove constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param WhitelistManagementInterface $whitelistService
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        WhitelistManagementInterface $whitelistService
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->whitelistService = $whitelistService;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            foreach ($collection->getItems() as $whitelist) {
                $result = $this->whitelistService->approve($whitelist->getId());
                if (!$result) {
                    throw new LocalizedException(__('Something went wrong trying to approve %1.', $whitelist->getValue()));
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
