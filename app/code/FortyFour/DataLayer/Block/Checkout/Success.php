<?php

namespace FortyFour\DataLayer\Block\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleTagManager\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

class Success extends Template
{
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Json
     */
    private $json;

    /**
     * Success constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $helper
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $helper,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->json = $json;
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        if (!$this->helper->isGoogleAnalyticsAvailable()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return string|null
     */
    public function getPurchaseEventJson(): ?string
    {
        try {
            $order = $this->checkoutSession->getLastRealOrder();
            $data =  [
                'event' => 'Purchase',
                'ecommerce' => [
                    'purchase' => [
                        'actionField' => [
                            'id' => $order->getIncrementId(),
                            'affiliation' => sprintf(
                                '%s - %s',
                                $this->_storeManager->getStore()->getWebsite()->getName(),
                                $this->_storeManager->getStore()->getName()
                            ),
                            'revenue' => $order->getGrandTotal(),
                            'tax' => $order->getTaxAmount(),
                            'shipping' => $order->getShippingAmount(),
                            'coupon' => $order->getCouponCode() ?: ''
                        ],
                        'products' => $this->getProducts($order)
                    ]
                ]
            ];

            return $this->json->serialize($data);
        } catch (\Exception $e) {
            $this->_logger->debug(__('[Success::getPurchaseEvent()] %1', $e->getMessage()));
            return null;
        }
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getProducts(Order $order): array
    {
        $products = [];
        /** @var Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $products[] = [
                'name' => $item->getName(),
                'id' => $item->getId(),
                'price' => $item->getBasePrice(),
                'quantity' => $item->getQtyOrdered(),
                'coupon' => $order->getCouponCode() ?: '',
            ];
        }

        return $products;
    }
    /**
     * @param $id
     * @param $name
     * @param $price
     * @return string|null
     */
    public function getWidgetProducts($id, $name, $price): ?string
    {
        try {
            $data =  [
                'ecommerce' => [
                    'add' => [
                        'products' => $this->getWidget($id,$name,$price)
                    ],
                    'event' => 'addToCart'
                ]
            ];

            return $this->json->serialize($data);
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            return null;
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $price
     * @return array
     */
    private function getWidget($id, $name, $price): array
    {
        $widgetProducts[] = [
            'name' => $name,
            'id' => $id,
            'price' => $price
        ];

        return $widgetProducts;
    }
}
