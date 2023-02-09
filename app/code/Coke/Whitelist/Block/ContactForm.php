<?php

namespace Coke\Whitelist\Block;

use Coke\Whitelist\Model\WhiteListHelper;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class ContactForm extends Template
{
    /**
     * @var WhiteListHelper
     */
    private $whiteListHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * ContactForm constructor.
     * @param Template\Context $context
     * @param WhiteListHelper $whiteListHelper
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        WhiteListHelper $whiteListHelper,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->whiteListHelper = $whiteListHelper;
        $this->logger = $logger;
    }

    public function getWhitelistSku()
    {
        return $this->getRequest()->getParam('sku');
    }

    public function getWhitelistRequestType()
    {
        return $this->getRequest()->getParam('request');
    }

    public function getWhitelistLabel()
    {
        if ($this->getWhitelistSku()) {
            $pledgeLabel = $this->whiteListHelper->getPledgeLabel($this->getWhitelistSku());
            return __("Please type the pledge or name you'd like to use for your can (\"%1\"). Keep in mind you have a maximum of 36 characters (including spaces) for a pledge and a maximum of 18 characters for a name. If you are requesting more than one name, please separate the names with commas.", $pledgeLabel);
        }
    }
}
