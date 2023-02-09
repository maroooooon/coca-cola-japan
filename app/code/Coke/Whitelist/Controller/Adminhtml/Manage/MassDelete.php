<?php

namespace Coke\Whitelist\Controller\Adminhtml\Manage;

use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Coke\Whitelist\Api\WhitelistManagementInterface;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Coke_Whitelist::whitelist_delete';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var WhitelistRepositoryInterface
     */
    private $whitelistRepository;

    /**
     * MassApprove constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param WhitelistRepositoryInterface $whitelistRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        WhitelistRepositoryInterface $whitelistRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->whitelistRepository = $whitelistRepository;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            foreach ($collection->getItems() as $whitelist) {
                $result = $this->whitelistRepository->delete($whitelist);
                if (!$result) {
                    throw new LocalizedException(__('Something went wrong trying to delete %1.', $whitelist->getValue()));
                }
            }

            $this->messageManager->addSuccessMessage(__('You deleted %1 record(s).', count($collection->getItems())));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
