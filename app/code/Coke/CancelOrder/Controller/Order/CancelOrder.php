<?php

namespace Coke\CancelOrder\Controller\Order;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Coke\CancelOrder\Logger\Logger;

class CancelOrder implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var ResultFactory
     */
    private $resultFactory;
    /**
     * @var Validator
     */
    private $formKeyValidator;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Context
     */
    private $httpContext;

    /**
     * @param RequestInterface $request
     * @param Logger $logger
     * @param OrderManagementInterface $orderManagement
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param Validator $formKeyValidator
     * @param OrderRepositoryInterface $orderRepository
     * @param Context $httpContext
     */
    public function __construct(
        RequestInterface $request,
        Logger $logger,
        OrderManagementInterface $orderManagement,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Validator $formKeyValidator,
        OrderRepositoryInterface $orderRepository,
        Context $httpContext
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->orderManagement = $orderManagement;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->orderRepository = $orderRepository;
        $this->httpContext = $httpContext;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->request->getParam('order_id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
            ->setPath('sales/order/view', ['order_id' => $orderId]);

        if (!$this->formKeyValidator->validate($this->request) || !$this->isCustomerLoggedIn()) {
            $this->messageManager->addErrorMessage(
                "Sorry, there has been an error processing your request. Please try again later."
            );
            return $resultRedirect;
        }

        try {
            $order = $this->orderRepository->get($orderId);
            if ($order->canCancel()) {
                $this->orderManagement->cancel($orderId);
                $this->messageManager->addSuccessMessage(__("You canceled the order."));
            } else {
                $this->messageManager->addErrorMessage("You have not canceled the item.");
                $this->logger->info(__('[CancelOrder] Unable to cancel Order: %1', $orderId));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage("Sorry, we are unable to cancel the order.");
            $this->logger->info(__('[CancelOrder] %1', $e->getMessage()));
        }


        return $resultRedirect;
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
