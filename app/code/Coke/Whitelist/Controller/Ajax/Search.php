<?php

namespace Coke\Whitelist\Controller\Ajax;

use Coke\Whitelist\Model\ResourceModel\Whitelist\Collection;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Coke\Whitelist\Model\Source\Status;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Search implements HttpGetActionInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var JsonFactory
     */
    private $jsonResultFactory;
    /**
     * @var CollectionFactory
     */
    private $whitelistColectionFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Search constructor.
     * @param LoggerInterface $logger
     * @param JsonFactory $jsonResultFactory
     * @param CollectionFactory $whitelistColectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        LoggerInterface $logger,
        JsonFactory $jsonResultFactory,
        CollectionFactory $whitelistColectionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->whitelistColectionFactory = $whitelistColectionFactory;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $resultJson = $this->jsonResultFactory->create();
        $typeId = $this->request->getParam('type_id');
        $term = $this->request->getParam('term');

        if (!$typeId || !is_numeric($typeId)) {
            return $resultJson->setHttpResponseCode(400)
                ->setData([
                    'error' => 'Type is not valid'
                ]);
        }

        if (strlen($term) < 2) {
            return $resultJson->setHttpResponseCode(400)
                ->setData([
                    'error' => 'Term missing'
                ]);
        }

        /** @var Collection $collection */
        $collection = $this->whitelistColectionFactory->create();
        $collection->addFieldToSelect(['entity_id', 'value'])->addFieldToFilter('type_id', $typeId)
            ->addFieldToFilter('value', ['like' => str_replace('\'', '\\\'', $term) . '%'])
            ->addFieldToFilter('store_id', $this->storeManager->getStore()->getId())
            ->addFieldToFilter('status', Status::APPROVED)
            ->setPageSize(10);

        return $resultJson->setData([
            'success' => true,
            'results' => $collection->getData(),
        ]);
    }
}
