<?php

namespace Coke\Japan\Observer;

use Aheadworks\Sarp2\Model\ResourceModel\Profile\Order\CollectionFactory;
use Coke\Japan\Model\SubscriptionDiscountAggregator;
use Coke\Japan\Model\Website;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class AddAttributesToLogicBrokerOrder implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    private $profileOrderCollectionFactory;
    /**
     * @var SubscriptionDiscountAggregator
     */
    private $subscriptionDiscountAggregator;

    /**
     * AddAttributesToLogicBrokerOrder constructor.
     * @param CollectionFactory $profileOrderCollectionFactory
     */
    public function __construct(
        CollectionFactory $profileOrderCollectionFactory,
        SubscriptionDiscountAggregator $subscriptionDiscountAggregator
    )
    {
        $this->profileOrderCollectionFactory = $profileOrderCollectionFactory;
        $this->subscriptionDiscountAggregator = $subscriptionDiscountAggregator;
    }

    public function execute(Observer $observer)
    {
        $data = $observer->getEvent()->getDataObject();
        /** @var Order $order */
        $order = $data->getOrder();
        $apiOrder = $data->getApiOrder();

        // Is japan?
        if ($order->getStore()->getWebsite()->getCode() !== Website::MARCHE) {
            return;
        }

        $this->addRewardPointsUsage($apiOrder, $order);
        $discountAdded = $this->addSubscriptionDiscountToOverallDiscount($apiOrder, $order);
        $this->addSubscriptionDiscountToSubTotal($apiOrder, $discountAdded, $order);

        $data->setApiOrder($apiOrder);
    }

    public function addRewardPointsUsage(&$apiOrder, $order)
    {
        if ($order->getData('reward_points_balance') !== null) {
            $apiOrder['ExtendedAttributes'][] = ['Name' => 'RewardPointsUsage', 'Value' => $order->getData('reward_points_balance')];
        }
    }

    public function addSubscriptionDiscountToOverallDiscount(&$apiOrder, $order): float
    {
        $discount = $this->subscriptionDiscountAggregator->getOrderDiscount($order);

        if ($discount === 0.00) {
            return 0.00;
        }

        if (!isset($apiOrder['Discounts'])) {
            $apiOrder["Discounts"] = [["DiscountAmount" => 0, "DiscountName" => "Total Discount"]];
        }

        // japan rounds down their prices, so we have round up our discounts.
        // $totalDiscount = ceil($discount);
        $totalDiscount = $discount;
        $apiOrder['Discounts'][0]['DiscountAmount'] += $totalDiscount;

        return $totalDiscount;
    }

    public function addSubscriptionDiscountToSubTotal(&$apiOrder, $discountAmount, Order $order)
    {
//        if (!isset($apiOrder["SubTotal"])) {
//            $apiOrder["SubTotal"] = $apiOrder["TotalAmount"];
//        }
        $apiOrder["SubTotal"] = $order->getSubtotal() + $discountAmount;
    }
}
