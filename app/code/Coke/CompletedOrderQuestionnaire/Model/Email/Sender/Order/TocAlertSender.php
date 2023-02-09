<?php

namespace Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Order;

use Coke\CompletedOrderQuestionnaire\Model\Email\Container\Order\AlertTocIdentity;
use Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Sender;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class TocAlertSender extends Sender
{
    /**
     * TOC Alert Sender constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param AlertTocIdentity $identityContainer
     * @param StoreManagerInterface $storeManager
     * @param Template $templateContainer
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        AlertTocIdentity $identityContainer,
        StoreManagerInterface $storeManager,
        Template $templateContainer
    ) {
        parent::__construct(
            $context,
            $logger,
            $transportBuilder,
            $identityContainer,
            $storeManager,
            $templateContainer
        );
    }

    /**
     * @param $params
     * @param string $recipientEmail
     * @return bool
     */
    public function send($params, string $recipientEmail)
    {
        try {
            return $this->sendEmail($params, $recipientEmail);
        } catch (LocalizedException $e) {
            $this->logger->critical(__($e->getMessage()));
        }
    }
}
