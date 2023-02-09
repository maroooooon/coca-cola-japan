<?php

namespace FortyFour\AgeRestriction\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Title
     */
    protected $title;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Title $title
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Title $title
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->title = $title;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($this->title->getDefault());

        return $resultPage;
    }
}
