<?php

namespace Coke\OLNB\ViewModel;

use Coke\OLNB\Helper\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class LanguageChanger extends \Magento\Store\ViewModel\SwitcherUrlProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CollectionFactory
     */
    private $storeCollectionFactory;
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * LanguageChanger constructor.
     * @param EncoderInterface $encoder
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $storeCollectionFactory
     * @param Config $configHelper
     */
    public function __construct(
        EncoderInterface $encoder,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $storeCollectionFactory,
        Config $configHelper
    ) {
        parent::__construct($encoder, $storeManager, $urlBuilder);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        if (!$this->configHelper->shouldShowLanguageChanger()) {
            return [];
        }

        $overrides = [
            'nl_BE' => 'NL',
        ];

        $stores = $this->storeCollectionFactory->create()
            ->addFieldToFilter('group_id', $this->storeManager->getGroup()->getId())
            ->getItems();

        $locales = [];
        foreach ($stores as $store) {
            $locale = $this->getStoreLocale($store->getId());

            if (isset($overrides[$locale])) {
                $locales[$overrides[$locale]] = $store;
                continue;
            }

            $locale = explode("_", $locale);

            if (isset($locale[1])) {
                $locales[$locale[1]] = $store;
            }
        }

        return $locales;
    }

    protected function getStoreLocale($storeId)
    {
        return $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $storeId);
    }
}
