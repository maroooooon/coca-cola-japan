<?php

namespace Coke\Logicbroker\Cron;

use Coke\Logicbroker\Preference\Helper;
use Coke\Sarp2\Helper\Order\SubscriptionChecker;
use Logicbroker\RetailerAPI\Helper\Data;
use Logicbroker\RetailerAPI\Jobs\Cron\SendOrders;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\GiftMessage\Api\OrderRepositoryInterface as GiftMessageRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\TransactionSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class LogicbrokerSendOrders extends SendOrders
{
	protected Manager$eventManager;
	protected LoggerInterface $logger;
	protected StoreManagerInterface $storeManager;
	private CollectionFactory $productOptionCollectionFactory;
	private AttributeCollectionFactory $collectionFactory;
	private SubscriptionChecker $subscriptionChecker;
	private OrderItemCollectionFactory $orderItemCollectionFactory;
	private TransactionSearchResultInterface $transactionSearchResult;
	private Helper $logicbrokerHelper;
	private ProductRepository $productRepository;

	/**
	 * @param OrderRepositoryInterface $orderRepository
	 * @param GiftMessageRepositoryInterface $giftRepo
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param Data $helper
	 * @param Manager $eventManager
	 * @param LoggerInterface $logger
	 * @param StoreManagerInterface $storeManager
	 * @param CollectionFactory $productOptionCollectionFactory
	 * @param AttributeCollectionFactory $collectionFactory
	 * @param SubscriptionChecker $subscriptionChecker
	 * @param OrderItemCollectionFactory $orderItemCollectionFactory
	 * @param TransactionSearchResultInterface $transactionSearchResult
	 * @param Helper $logicbrokerHelper
	 * @param ProductRepository $productRepository
     * @param Emulation $emulation
	 */
    public function __construct(
	    OrderRepositoryInterface         $orderRepository,
	    GiftMessageRepositoryInterface   $giftRepo,
	    SearchCriteriaBuilder            $searchCriteriaBuilder,
	    Data                             $helper,
	    Manager                          $eventManager,
	    LoggerInterface                  $logger,
	    StoreManagerInterface            $storeManager,
	    CollectionFactory                $productOptionCollectionFactory,
	    AttributeCollectionFactory       $collectionFactory,
	    SubscriptionChecker              $subscriptionChecker,
	    OrderItemCollectionFactory       $orderItemCollectionFactory,
		TransactionSearchResultInterface $transactionSearchResult,
	    Helper $logicbrokerHelper,
	    ProductRepository $productRepository,
        Emulation $emulation
    )
    {
        parent::__construct($orderRepository, $giftRepo, $searchCriteriaBuilder, $helper, $emulation);
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->productOptionCollectionFactory = $productOptionCollectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->subscriptionChecker = $subscriptionChecker;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
	    $this->transactionSearchResult = $transactionSearchResult;
	    $this->logicbrokerHelper = $logicbrokerHelper;
		$this->productRepository = $productRepository;
    }

    protected function transmitOrder($order, $includeBundle)
    {
        $this->helper->setApiKey(null);
        $this->storeManager->setCurrentStore($order->getStoreId());
        parent::transmitOrder($order, $includeBundle);
    }

    protected function convertOrder($order, $includeBundle)
    {
        $apiOrder = parent::convertOrder($order, $includeBundle);

	/* Checking if the payment method is Stripe and if the order total is greater than 0. If it is, then it
	will add the payment details to the order. */
	$storeId = (int) $order->getStoreId();
	$sendTransactionDetailsIsEnabled = $this->logicbrokerHelper->getSendTransactionDetailsIsEnabled($storeId);
	$isPayment = $order->getPayment()->getAmountOrdered() > 0;
	$isStripe = $order->getPayment()->getMethod() == 'stripe_payments';

	if ($sendTransactionDetailsIsEnabled && $isPayment && $isStripe) {
	/* If the result of $this->getPaymentDetails() returns null, then return null to skip the order */
	/* If not, then add the payment details to the order. */
            if (!$apiOrder['Payments'][] = $this->getPaymentDetails($order)) {
                return null;
            }
		}

        if ($order->getDeliveryDate()) {
            $this->setExtendedData(
                $apiOrder['ExtendedAttributes'],
                "DeliveryDate",
                stristr($order->getDeliveryDate(), ' ', true)
            );
        }

        if ($order->getDeliveryComment()) {
            $this->setExtendedData(
                $apiOrder['ExtendedAttributes'],
                "DeliveryComment",
                $order->getDeliveryComment()
            );
        } else if ($order->getExpressStandardDeliveryComment()) {
            $this->setExtendedData(
                $apiOrder['ExtendedAttributes'],
                "DeliveryComment",
                $order->getExpressStandardDeliveryComment()
            );
        }

        if ($order->getCouponCode()) {
            $this->setExtendedData(
                $apiOrder['ExtendedAttributes'],
                "PromoCode",
                $order->getCouponCode()
            );
        }

        $dataObject = new DataObject([
            'api_order' => $apiOrder,
            'order' => $order,
            'include_bundle' => $includeBundle,
        ]);
        $this->eventManager->dispatch('logicbroker_after_convert_order', [
            'data_object' => $dataObject,
        ]);

        $this->logger->debug(sprintf(
            'Sending order to LogicBroker: %s',
            print_r($dataObject->getData('api_order'), true)
        ));

        return $dataObject->getData('api_order');
    }

    /**
     * @param \Magento\Sales\Model\Order\Address $address
     * @return array
     */
    protected function getContact($address)
    {
        $contact = array(
            "FirstName" => $address->getFirstName(),
            "LastName" => $address->getLastName(),
            "CompanyName" => $address->getCompany(),
            "Email" => $address->getEmail(),
            "City" => $address->getCity(),
            "State" => $address->getRegion(), // used to be $address->getRegionCode(), MARCHE-338
            "Zip" => $address->getPostcode(),
            "Country" => $address->getCountryId(),
            "Phone" => $address->getTelephone()
        );
        $street = $address->getStreet();
        if ($street != null) {
            if (count($street) > 0) {
                $contact["Address1"] = $street[0];
            }
            if (count($street) > 1) {
                $contact["Address2"] = implode(' ', array_splice($street, 1));
            }
        }
        return $contact;
    }

    protected function getItems($items, $includeBundle)
    {
        $orderId = array_reduce($items, function ($carry, $item) {
            return $item['order_id'];
        }, 0);

        if (!$this->subscriptionChecker->hasQuoteId($orderId) && $this->subscriptionChecker->isSubscription($orderId)) {
            $items = $this->getOrderItemsByOrderId($orderId);
        }

        return parent::getItems($items, $includeBundle);
    }

    /**
     * @param int $orderId
     * @return mixed
     */
    private function getOrderItemsByOrderId(int $orderId)
    {
        $collection = $this->orderItemCollectionFactory->create();
        $collection->addFieldToFilter('order_id', ['eq' => $orderId])
            ->setOrder('sarp2_profile_item_id', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        return $collection;
    }

    /**
     * @inheritDoc
     */
    protected function toApiItem($item, $childItem = null)
    {
		$name = $item->getName();
        $cost = $item->getBaseCost();
	    $cokeLayoutId = null;

        if ($childItem != null) {
            $name = $childItem->getName();
            $cost = $childItem->getBaseCost();

			$product = $this->productRepository->get($childItem->getProduct()->getSku(), false, $item->getOrder()->getStore()->getId());
			if ($product->getCustomAttribute('coke_layout_id')) {
				$cokeLayoutId = $product->getCustomAttribute('coke_layout_id')->getValue();
			}
        }

        $attrs = [0 => ["Name" => "item_id", "Value" => $item->getItemId()]];
	    if ($cokeLayoutId){
		    $attrs[] = ["Name" => "Coke Layout Id", "Value" => $cokeLayoutId ];
	    }
	    if ($item->getModerationStatus()) {
		    $attrs[] = ["Name" => "Moderation Status", "Value" => $item->getModerationStatus()];
	    }


        $attrs = array_merge($attrs, $this->getItemAttributes($item));

        $apiItem = [
            "ItemIdentifier" => [
                "SupplierSKU" => $item->getSku(),
                "PartnerSKU" => $item->getSku(),
                "UPC" => $item->getProduct() ? $item->getProduct()->getUpc() : null
            ],
            "Quantity" => floor(floatval($item->getQtyOrdered())),
            "Description" => html_entity_decode($name),
            "Price" => $this->getItemPrice($item),
            "Cost" => $cost,
            "Weight" => $item->getWeight(),
            "ExtendedAttributes" => $attrs
        ];
        $discAmt = $item->getDiscountAmount();
        $taxAmt = $item->getTaxAmount();
        if ($discAmt != 0) {
            $apiItem["Discounts"] = [
                [
                    "DiscountAmount" => $discAmt,
                    "DiscountName" => "Magento"
                ]
            ];
        }
        if ($taxAmt != 0) {
            $apiItem["Taxes"] = [
                [
                    "TaxAmount" => $taxAmt,
                    "TaxTitle" => "Magento"
                ]
            ];
        }

        $dataObject = new DataObject([
            'api_item' => $apiItem,
            'item' => $item,
            'child_item' => $childItem,
        ]);
        $this->eventManager->dispatch('logicbroker_after_to_api_item', [
            'data_object' => $dataObject,
        ]);

        return $dataObject->getData('api_item');
    }

    /**
     * @param OrderItemInterface $item
     * @return float|null
     */
    private function getItemPrice(OrderItemInterface $item)
    {
        return ($item->getParentItemId()) ? $item->getOriginalPrice() : $item->getPrice();
    }

    protected function getItemAttributes($item)
    {
        $attributes = [];
        $options = $item->getProductOptions();
        if (is_array($options) && isset($options["attributes_info"])) {
            foreach ($options["attributes_info"] as $attr) {
                if (is_array($attr) && isset($attr['label'], $attr['value'])) {
                    $attributes[] = [
                        "Name" => "Magento_".$attr["label"],
                        "Value" => $attr["value"],
                        "Section" => "Portal"
                    ];

                    if (isset($attr['option_id'])) {
                        /** @var \Magento\Eav\Model\ResourceModel\Attribute\Collection $attrCollection */
                        $attrCollection = $this->collectionFactory->create();
                        $attrCollection->addFieldToFilter('attribute_id', ['eq' => $attr['option_id']]);
                        $attrCollection->setPageSize(1);
                        $found = $attrCollection->getData();

                        if (count($found) > 0) {
                            $foundAttr = current($found);

                            $attributes[] = [
                                "Name" => "Magento_Attribute_" . $foundAttr['attribute_code'],
                                "Value" => $attr["value"],
                                "Section" => "Portal"
                            ];
                        }
                    }
                }
            }
        }


        // extra
        $add = [];

        if (empty($options['options'])) {
            return $attributes;
        }

        $optionIds = array_column($options['options'], 'option_id');
        $collection = $this->productOptionCollectionFactory->create();
        $optionModels = $collection->addFieldToFilter('option_id', ['in' => $optionIds])
            ->getItems();

        foreach ($options['options'] as $option) {
            $info = [
                "Name" => $option['label'],
                "Value" => $option['print_value'] ?? $option['option_value'] ?? $option['value'] ?? '',
                "Section" => "Portal",
            ];

            $add[] = $info;

            $optionModel = $optionModels[$option['option_id']] ?? null;
            if ($optionModel !== null) {
                if ($optionModel->getData('step_id')) {
                    $add[] = [
                        "Name" => 'Option_Step_' . $optionModel->getData('step_id'),
                        "Value" => $option['print_value'] ?? $option['option_value'] ?? $option['value'] ?? '',
                        "Section" => "Portal",
                    ];
                }
            }

        }

        return array_merge($attributes, $add);
    }


	/**
	 * It returns an array of payment details for a given order
	 * @param Order $order The order object
	 *
	 * @return array|null An array of payment details.
	 */
	public function getPaymentDetails(Order $order): ?array
	{
		/** @var Order\Payment\Transaction $transaction */
		$transaction = $this->transactionSearchResult->addOrderIdFilter($order->getId())->getFirstItem();
		$referenceId = $transaction->getAdditionalInformation('charge_transaction_id');

		/* Checking if the referenceId is set, if it is not, it means that the transaction is incomplete and should be skipped. */
		if (!$referenceId) {
			return null;
		}

		return  [
			'Method' => $order->getPayment()->getMethod(),
			'PaymentDate' => $transaction->getCreatedAt(),
			'Amount' => (float) $order->getPayment()->getAmountPaid(),
			'ReferenceId' => $referenceId
		];
	}
}
