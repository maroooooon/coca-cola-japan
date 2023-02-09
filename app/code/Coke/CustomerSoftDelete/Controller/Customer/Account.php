<?php

namespace Coke\CustomerSoftDelete\Controller\Customer;

use Coke\Sarp2\Service\Subscription as SubscriptionService;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Psr\Log\LoggerInterface;

class Account implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var \Magento\Framework\Controller\Result\Redirect
     */
    private $resultFactory;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var SubscriptionManagerInterface
     */
    private $subscriptionManager;
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @param CustomerSession $customerSession
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionManagerInterface $subscriptionManager,
        SubscriptionService $subscriptionService
    ){
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->subscriptionManager = $subscriptionManager;
        $this->subscriptionService = $subscriptionService;
    }

    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();
        try {
            $customerId = $this->customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            $this->subscriptionService->cancelAllCustomerSubscriptions($customer);
            $originalEmail = $customer->getEmail();
            $customer->setEmail('disabled_' . time() . '_' . $originalEmail);
            $this->customerRepository->save($customer);
            $this->subscriptionManager->unsubscribeCustomer($customer->getId(), $customer->getStoreId());
            $connection->update(
                $connection->getTableName('customer_entity'),
                ['original_email' => $originalEmail],
                ['entity_id = ?' => (int)$customerId]
            );
            $this->customerSession->destroy();
            $this->messageManager->addSuccessMessage(__('Your Customer Account Has Been Deleted.'));
            $connection->commit();
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $redirect->setPath('/');
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(__('There was a problem deleting your account. Please contact customer support.'));
            $connection->rollBack();
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $redirect->setPath('customer/account');
        }
    }
}
