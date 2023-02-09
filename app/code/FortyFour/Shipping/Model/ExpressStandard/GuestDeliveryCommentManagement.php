<?php

namespace FortyFour\Shipping\Model\ExpressStandard;

use FortyFour\Shipping\Api\ExpressStandard\DeliveryCommentManagementInterface;
use FortyFour\Shipping\Api\ExpressStandard\GuestDeliveryCommentManagementInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Psr\Log\LoggerInterface;

class GuestDeliveryCommentManagement implements GuestDeliveryCommentManagementInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;
    /**
     * @var DeliveryCommentManagementInterface
     */
    private $deliveryCommentManagement;

    /**
     * OrderService constructor.
     * @param LoggerInterface $logger
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param DeliveryCommentManagementInterface $deliveryCommentManagement
     */
    public function __construct(
        LoggerInterface $logger,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        DeliveryCommentManagementInterface $deliveryCommentManagement
    ) {
        $this->logger = $logger;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->deliveryCommentManagement = $deliveryCommentManagement;
    }

    /**
     * @inheritDoc
     */
    public function saveDeliveryComment($cartId, $deliveryComment)
    {
        $cartId = $this->maskedQuoteIdToQuoteId->execute($cartId);
        return $this->deliveryCommentManagement->saveDeliveryComment($cartId, $deliveryComment);
    }

}
