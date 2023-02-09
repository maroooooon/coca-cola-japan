<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2020 Classy Llama Studios, LLC
 */

namespace Zendesk\Zendesk\Observer;

use Magento\Framework\Event\Observer;
use Zendesk\Zendesk\Model\Config\ConfigProvider;
use Zendesk\Zendesk\Helper\Sunshine;

class OrderPaid extends Base
{

    /**
     * Event name: çsales_order_invoice_pay
     * This event handles order invoiced
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->isEnabled(ConfigProvider::XML_PATH_EVENT_ORDER_PAID)) {
            return;
        }

        $this->observer = $observer;
        $this->observerType = $observer->getEvent()->getname();

        // check if user was logged in
        if($this->observer->getInvoice()->getCustomerId() === null) {
            return;
        }

        try {
            $this->createEvent();
        } catch (\Exception $exception) {
            $this->logError($exception->getMessage());
            return;
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getSunshineEvent()
    {
        try {
            $customerId = $this->observer->getInvoice()->getCustomerId();
            $customer = $this->getCustomerById($customerId);
            $orderId = $this->observer->getInvoice()->getOrderId();
            $order = $this->getOrderById($orderId); // get order, because invoice doesn't have address information.
            $items = $order->getItems();
            $itemArray = $this->makeItemArray($items);

            $payload = [
                'event' => [
                    'created_at' => date('c'),
                    'description' => "order invoiced",
                    'properties' => [
                        'line_items' => $itemArray,
                        'total price' => number_format($order->getGrandTotal(), 2, '.', ','),
                        'fulfilment status.' => $order->getStatus()
                    ],
                    'source' => Sunshine::IDENTIFIER,
                    'type' => 'order invoiced'
                ],
                'profile' => [
                    'identifiers' => [
                        [
                            'type' => 'email',
                            'value' => $order->getCustomerEmail()
                        ],
                        [
                            'type' => 'id',
                            'value' => strval($order->getCustomerId())
                        ]
                    ],
                    'attributes' => [
                        'first name' => $customer->getFirstname(),
                        'last name' => $customer->getLastname(),
                        'orders count' => $this->getTotalOrders($customer->getId()),
                        'total spent' => $this->getTotalSpent($customer->getId())
                    ],
                    'source' => Sunshine::IDENTIFIER,
                    'type' => Sunshine::PROFILE_TYPE
                ]
            ];
            // add values that might not have a value, so that that I can only add them if they exist.
            $this->getShippingAddressFromOrder($order) && $this->getShippingAddressFromOrder($order)->getTelephone() ? $payload['profile']['attributes']['phone'] = $this->getShippingAddressFromOrder($order)->getTelephone() : null;
            $this->getShippingAddressArrayFromOrder($order) ? $payload['profile']['attributes']['address'] = $this->getShippingAddressArrayFromOrder($order) : null;
            $this->getShippingAddressArrayFromOrder($order) ? $payload['event']['properties']['shipping address'] = $this->getShippingAddressArrayFromOrder($order) : null;

            return $payload;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $this->logError($exception->getMessage());
            return [];
        }
    }

    /**
     * @return string
     */
    protected function getCustomerEmail()
    {
        $this->observer->getOrder()->getEmail();
        return $this->_customerSession->getCustomer()->getEmail();
    }

    /**
     * @param $order
     * @return array|mixed
     */
    protected function getShippingAddressFromOrder($order)
    {
        try {
            $addresses = $order->getAddresses();
            if (count($addresses) === 1) {
                return array_shift($addresses);
            }
            foreach ($addresses as $address) {
                if ($address->getAddressType() == 'shipping') {
                    return $address;
                }
            }
            return array_shift($addresses);
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $order
     * @return array|null
     */
    protected function getShippingAddressArrayFromOrder($order)
    {
        $address = $this->getShippingAddressFromOrder($order);
        if (!$address) {
            return null;
        }
        $addressArray = [];
        $address->getStreet()[0] ? $addressArray['address1'] = $address->getStreet()[0] : null;
        count($address->getStreet()) > 1 ? $addressArray['address2'] = $address->getStreet()[1] : null;
        $address->getCity() ? $addressArray['city'] = $address->getCity() : null;
        $address->getRegion() ? $addressArray['province'] = $address->getRegion() : null;
        $address->getCountryId() ? $addressArray['country'] = $this->getCountryName($address->getCountryId()) : null;
        $address->getPostcode() ? $addressArray['zip'] = $address->getPostcode() : null;
        return $addressArray;
    }
}
