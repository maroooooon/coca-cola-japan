<?php

namespace FortyFour\Shipping\Model\ExpressStandard;

use FortyFour\Shipping\Api\ExpressStandard\DeliveryCommentManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeliveryCommentManagement implements DeliveryCommentManagementInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * OrderService constructor.
     * @param LoggerInterface $logger
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CartRepositoryInterface $cartRepository
    ) {
        $this->logger = $logger;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritDoc
     */
    public function saveDeliveryComment($cartId, $deliveryComment)
    {
        try {
            $quote = $this->cartRepository->get($cartId);
            $quote->setData(DeliveryComment::EXPRESS_STANDARD_DELIVERY_COMMENT, $deliveryComment);
            $this->cartRepository->save($quote);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
