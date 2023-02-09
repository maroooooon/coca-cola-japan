<?php
namespace Coke\Sarp2\Controller\Profile\Edit;

use Aheadworks\Sarp2\Controller\Profile\AbstractProfile;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Coke\Sarp2\Model\Profile\View\Action\Permission as CokeSarp2ActionPermission;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Aheadworks\Sarp2\Model\Profile\View\Action\Permission as ActionPermission;

class SkipNextPaymentDate extends AbstractProfile
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var CokeSarp2ActionPermission
     */
    private $cokeSarp2ActionPermission;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProfileRepositoryInterface $profileRepository
     * @param Session $customerSession
     * @param Registry $registry
     * @param ActionPermission $actionPermission
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProfileRepositoryInterface $profileRepository,
        Session $customerSession,
        Registry $registry,
        ActionPermission $actionPermission,
        CokeSarp2ActionPermission $cokeSarp2ActionPermission
    ) {
        parent::__construct($context, $profileRepository, $customerSession, $registry, $actionPermission);
        $this->resultPageFactory = $resultPageFactory;
        $this->cokeSarp2ActionPermission = $cokeSarp2ActionPermission;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Skip this Subscription'));

            $this
                ->registerProfile()
                ->setUrlToBackLink($resultPage);
            return $resultPage;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function isActionAllowed()
    {
        $profileId = $this->getProfile()->getProfileId();
        return $this->cokeSarp2ActionPermission->isSkipNextPaymentDateActionAvailable($profileId);
    }
}
