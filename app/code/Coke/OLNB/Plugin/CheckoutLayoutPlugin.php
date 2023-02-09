<?php

namespace Coke\OLNB\Plugin;

use Coke\OLNB\Helper\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CheckoutLayoutPlugin
{
    const IRELAND_STORE_CODES = ['ireland_english'];
    const TURKEY_STORE_CODES = ['turkey_turkish'];

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * CheckoutLayoutPlugin constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param UrlInterface $url
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        UrlInterface $url
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->url = $url;
    }

    /**
     * @param LayoutProcessor $subject
     * @param $jsLayout
     * @return mixed
     */
    public function afterProcess(
        LayoutProcessor $subject,
        $jsLayout
    ) {
        $jsLayout = $this->hideCityStateOnCheckout($jsLayout);
        $jsLayout = $this->addTurkeyDataConsent($jsLayout);

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function hideCityStateOnCheckout($jsLayout)
    {
        if (!$this->config->shouldHideCityStateOnCheckout()) {
            return $jsLayout;
        }

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region'],
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']
        );

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    private function addTurkeyDataConsent($jsLayout)
    {
        if ($this->customerSession->isLoggedIn()) {
            return $jsLayout;
        }

        try {
            $store = $this->storeManager->getStore();

            if (!in_array($store->getCode(), self::TURKEY_STORE_CODES)) {
                return $jsLayout;
            }
        } catch (\Exception $e) {
            $this->logger->critical(
                __('[\Coke\OLNB\Plugin\CheckoutLayoutPlugin::addTurkeyDataConsent()] %1',
                    $e->getMessage())
            );
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['data_consent_field'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'Coke_OLNB/form/element/checkbox',
            ],
            'dataScope' => 'shippingAddress.custom_attributes.data_consent_field',
            'label' => __('Coca Cola\'nın, kişisel verilerimin işlenmesine yönelik olarak sunduğu <a href="%1" target="_blank">Aydınlatma Metni’ni</a> okudum, anladım.', $this->url->getUrl('daha-daha-aydinlatma-metni')),
            'provider' => 'checkoutProvider',
            'sortOrder' => 150,
            'validation' => [
                'required-entry' => true
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => ''
        ];

        return $jsLayout;
    }
}
