<?php

namespace FortyFour\ShareSite\Block\Html;

use Magento\Framework\View\Element\Template;

class ShareSite extends Template
{

    /**
     * ShareSite constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
