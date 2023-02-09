<?php
namespace Zendesk\Zendesk\Observer;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zendesk\Zendesk\Helper\AllCustomerOrders;
use Zendesk\Zendesk\Helper\Sunshine;
use Zendesk\Zendesk\Model\Config\ConfigProvider;
use Zendesk\Zendesk\Registry\EventsRegistry;

abstract class Base implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var ConfigProvider $configProvider
     */
    protected $configProvider;

    /**
     * @var Observer $observer
     */
    protected $observer;

    /**
     * @var Sunshine $sunshineHelper
     */
    protected $sunshineHelper;

    /**
     * @var AllCustomerOrders
     */
    protected $orderHelper;

    /**
     * @var StoreManagerInterface $storeManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var LoggerInterface $loggerInterface
     */
    protected $loggerInterface;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var EventsRegistry
     */
    protected $eventList;

    /**
     * Base constructor.
     * @param Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ConfigProvider $configProvider
     * @param Sunshine $sunshineHelper
     * @param AllCustomerOrders $orderHelper
     * @param StoreManagerInterface $storeManagerInterface
     * @param LoggerInterface $loggerInterface
     * @param CountryFactory $countryFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        ConfigProvider $configProvider,
        Sunshine $sunshineHelper,
        AllCustomerOrders $orderHelper,
        StoreManagerInterface $storeManagerInterface,
        LoggerInterface $loggerInterface,
        CountryFactory $countryFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        OrderRepositoryInterface $orderRepository,
        EventsRegistry $eventList
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->configProvider = $configProvider;
        $this->sunshineHelper = $sunshineHelper;
        $this->orderHelper = $orderHelper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->loggerInterface = $loggerInterface;
        $this->countryFactory = $countryFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->orderRepository = $orderRepository;
        $this->eventList = $eventList;
    }

    /**
     * @param string $xmlPath
     * @return bool
     */
    protected function isEnabled($xmlPath)
    {
        return (bool) $this->configProvider->getValue($xmlPath);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    protected function getStoreId()
    {
        try {
            $store = $this->storeManagerInterface->getStore();
            return $store->getId();
        } catch (NoSuchEntityException $noSuchEntityException) {
            return null;
        }
    }

    /**
     * @param $id
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerById($id)
    {
        try {
            return $this->customerRepositoryInterface->getById($id);
        } catch (NoSuchEntityException $exception) {
            $this->logError($exception->getMessage());
            return null;
        }
    }

    /**
     * @param null $id
     * @return int|void|null
     */
    protected function getTotalOrders($id = null)
    {
        if ($id) {
            return $this->orderHelper->getTotalNumberOrders($id);
        } else {
            return $this->getCustomerId() ? $this->orderHelper->getTotalNumberOrders($this->getCustomerId()) : null;
        }
    }

    /**
     * @param $id
     * @return string|null
     */
    protected function getTotalSpent($id = null)
    {
        if ($id) {
            return $this->orderHelper->getTotalSpent($id);
        } else {
            return $this->getCustomerId() ? $this->orderHelper->getTotalSpent($this->getCustomerId()) : null;
        }
    }

    /**
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    protected function createEvent()
    {

        if ($this->eventList->get()) {
            // If there is already an event created, we normally want to cancel the additional event, because if there's too many it creates unwanted events in Zendesk.
            // However there are occastional times when we need multiple events to send in one request.
            // To account for this, we have a hardcoded array of arrays here.
            // If the oldEvent and newEvent match any of the arrays, then it doesn't quit.

            // First in array is the existing event, second is the new event
            $dontSkipTheseEvents = [
                ["customer_address_save_after", "sales_order_save_after"]
            ];
            $previousEvent = $this->eventList->get()->getEvent()->getName();
            $newEvent = $this->observer->getEvent()->getname();
            $eventsMatch = false;
            // check if any events match any in the array
            foreach ($dontSkipTheseEvents as $event) {
                if($event[0] === $previousEvent && $event[1] === $newEvent) {
                    // if they match
                    $eventsMatch = true;
                }
            }
            if(!$eventsMatch) {
                // only return if none of the set groups of events, are currently happening;
                return;
            }
        }
        $this->eventList->set($this->observer);
        $this->sunshineHelper->_endpoint = 'api/v2/user_profiles/events';
        $payload = $this->getSunshineEvent();
        if ($payload) {
            $this->sunshineHelper->post($payload);
        }
    }

    /**
     * @return \Magento\Quote\Model\Quote|null
     */
    protected function getCart()
    {
        try {
            return $this->checkoutSession->getQuote();
        } catch (\Exception $exception) {
            $this->logError($exception->getMessage());
            return null;
        }

    }

    /**
     * @return string
     */
    protected function getCustomerEmail()
    {
        return $this->_customerSession->getCustomer()->getEmail();
    }

    /**
     * @return mixed
     */
    protected function getCustomerId()
    {
        return $this->_customerSession->getCustomer()->getEntityId();
    }

    /**
     * @param $countryCode
     * @return string
     */
    protected function getCountryName($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * @return array|\Magento\Customer\Api\Data\AddressInterface|mixed
     */
    protected function getShippingAddress()
    {
        try {
            $addresses = $this->_customerSession->getCustomerData()->getAddresses();
            if (count($addresses) === 1) {
                return array_shift($addresses);
            }
            foreach ($addresses as $address) {
                if ($address->isDefaultShipping()) {
                    return $address;
                }
            }
            return array_shift($addresses);
        } catch (\Exception $exception) {
            $this->logError($exception->getMessage());
            return [];
        }
    }

    /**
     * @param $items
     * @return array
     */
    protected function makeItemArray($items)
    {
        $itemArray = [];
        foreach ($items as $item) {
            if (!$item->getParentItem()) {
                $data = [
                    'title' => $item->getName(),
                    'quantity' => number_format($item->getQtyOrdered() ?? $item->getQty(), 0, '.', ','),
                    'price' => number_format($item->getPrice(), 2, '.', ',')
                ];
                $itemArray [] = $data;
            }
        }

        return $itemArray;
    }

    /**
     * Add each item to address array, and if it doesn't exist, skip that line.
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getShippingAddressArray()
    {
        $address = $this->getShippingAddress();
        if (!$address) {
            return null;
        }
        $addressArray = [];
        $address->getStreet()[0] ? $addressArray['address1'] = $address->getStreet()[0] : null;
        count($address->getStreet()) > 1 ? $addressArray['address2'] = $address->getStreet()[1] : null;
        $address->getCity() ? $addressArray['city'] = $address->getCity() : null;
        $address->getRegion()->getRegion() ? $addressArray['province'] = $address->getRegion()->getRegion() : null;
        $address->getCountryId() ? $addressArray['country'] = $this->getCountryName($address->getCountryId()) : null;
        $address->getPostcode() ? $addressArray['zip'] = $address->getPostcode() : null;
        return $addressArray;
    }

    /**
     * @return bool
     */
    protected function isLoggedIn()
    {
        return !!$this->_customerSession->getCustomer()->getId();
    }

    /**
     * @param $id
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    protected function getOrderById($id)
    {
        return $this->orderRepository->get($id);
    }

    /**
     * @param $message
     */
    protected function logError($message)
    {
        if ($this->isEnabled(ConfigProvider::XML_PATH_DEBUG_ENABLED)) {
            $this->loggerInterface->error($message);
        }
    }
}
