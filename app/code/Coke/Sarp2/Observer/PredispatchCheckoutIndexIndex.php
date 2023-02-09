<?php

namespace Coke\Sarp2\Observer;

use Coke\Sarp2\Helper\ForceLogin as ForceLoginHelper;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;

class PredispatchCheckoutIndexIndex implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var RedirectInterface
     */
    private $redirect;
    /**
     * @var ForceLoginHelper
     */
    private $forceLoginHelper;

    /**
     * @param ForceLoginHelper $forceLoginHelper
     * @param ManagerInterface $messageManager
     * @param RedirectInterface $redirect
     */
    public function __construct(
        ForceLoginHelper  $forceLoginHelper,
        ManagerInterface  $messageManager,
        RedirectInterface $redirect
    ) {
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->forceLoginHelper = $forceLoginHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->forceLoginHelper->canProceedToCheckout()) {
            $this->messageManager->addError($this->forceLoginHelper->renderForceLoginMessage());
            $this->redirect->redirect($observer->getControllerAction()->getResponse(), 'checkout/cart');
        }
    }
}
