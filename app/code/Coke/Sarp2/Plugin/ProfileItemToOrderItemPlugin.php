<?php

namespace Coke\Sarp2\Plugin;

use Aheadworks\Sarp2\Api\Data\ProfileItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Psr\Log\LoggerInterface;

class ProfileItemToOrderItemPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * ProfileAddressToOrderPlugin constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param \Aheadworks\Sarp2\Model\Profile\Item\ToOrderItem $subject
     * @param $result
     * @param ProfileItemInterface $profileItem
     * @param $paymentPeriod
     * @param array $data
     * @return OrderItemInterface
     */
    public function afterConvert(
        \Aheadworks\Sarp2\Model\Profile\Item\ToOrderItem $subject,
        $result,
        ProfileItemInterface $profileItem,
        $paymentPeriod,
        $data = []
    ) {
        /** @var OrderItemInterface $result */
        $result->setData('sarp2_profile_item_id', $profileItem->getItemId());
        return $result;
    }
}
