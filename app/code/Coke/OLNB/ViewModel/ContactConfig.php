<?php

namespace Coke\OLNB\ViewModel;

use Coke\OLNB\Helper\ContactConfig as ContactConfigHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

class ContactConfig implements ArgumentInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ContactConfig
     */
    private $contactConfig;

    /**
     * @param LoggerInterface $logger
     * @param ContactConfig $contactConfig
     */
    public function __construct(
        LoggerInterface $logger,
        ContactConfigHelper $contactConfig
    ) {
        $this->logger = $logger;
        $this->contactConfig = $contactConfig;
    }

    /**
     * @return mixed
     */
    public function isDobEnabled()
    {
        return $this->contactConfig->isDobEnabled();
    }

    /**
     * @return mixed
     */
    public function isTelephoneEnabled()
    {
        return $this->contactConfig->isTelephoneEnabled();
    }
}
