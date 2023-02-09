<?php

namespace Coke\ContactAgeRestrict\Plugin\Controller\Account;

use Coke\ContactAgeRestrict\Helper\Data;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;

class CreatePostPlugin
{
    /**
     * @var ManagerInterface
     */
    private $message;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlModel;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var RedirectInterface
     */
    private $redirect;
    /**
     * @var Data
     */
    private $contactAgeRestrictHelper;

    public function __construct(
        ManagerInterface $message,
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        Data $data
    ){
        $this->message = $message;
        $this->urlModel = $urlFactory->create();
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
        $this->contactAgeRestrictHelper = $data;
    }


    public function aroundExecute(\Magento\Customer\Controller\Account\CreatePost $subject, callable $proceed)
    {
        try {
            if (!$subject->getRequest()->getParam('day') || !$subject->getRequest()->getParam('month') || !$subject->getRequest()->getParam('year')) {
                $this->message->addErrorMessage('Date of birth is required.');
                $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                return $this->redirectFactory->create()
                    ->setUrl($this->redirect->error($url));
            } else {
                $dob = new \DateTime($this->getFormattedDate($subject));
                $this->contactAgeRestrictHelper->validateAge($dob, false);
                $params = $subject->getRequest()->getParams();
                $subject->getRequest()->setParams($params);
                return $proceed();
            }
        } catch (\Exception $exception) {
            $this->message->addError($exception->getMessage());
            $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            return $this->redirectFactory->create()
                ->setUrl($this->redirect->error($url));
        }
    }

    /**
     * Return the formatted date for DOB validation
     *
     * @return string
     */
    private function getFormattedDate($subject)
    {
        $request = $subject->getRequest();

        return sprintf('%s-%s-%s',
            $request->getParam('year'),
            $request->getParam('month'),
            $request->getParam('day')
        );
    }
}
