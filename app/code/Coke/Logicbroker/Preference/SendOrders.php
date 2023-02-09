<?php

namespace Coke\Logicbroker\Preference;

use Logicbroker\RetailerAPI\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\GiftMessage\Api\OrderRepositoryInterface as GiftMessageRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManager;

class SendOrders extends \Logicbroker\RetailerAPI\Jobs\Cron\SendOrders
{
    /**
     * @var StoreManager
     */
    private $storeManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        GiftMessageRepositoryInterface $giftRepo,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $helper,
        StoreManager $storeManager,
        Emulation $emulation
    ) {
        parent::__construct($orderRepository, $giftRepo, $searchCriteriaBuilder, $helper, $emulation);
        $this->storeManager = $storeManager;
    }

}
