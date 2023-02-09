<?php

namespace FortyFour\Voucher\Model\Email\Sender;

use FortyFour\Voucher\Model\Email\Container\VouchersEmailIdentity;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class VouchersEmailSender extends Sender
{
    /**
     * VouchersEmailSender constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param VouchersEmailIdentity $identityContainer
     * @param StoreManagerInterface $storeManager
     * @param Template $templateContainer
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        VouchersEmailIdentity $identityContainer,
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
            $this->sendEmail($params, $recipientEmail);
            return true;
        } catch (LocalizedException $e) {
            $this->logger->debug(__($e->getMessage()));
            return false;
        }
    }

}
