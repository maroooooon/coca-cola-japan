<?php

namespace Coke\OLNB\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class FixedProductTax implements ArgumentInterface
{
    /**
     * @var string
     */
    const FPT_BOTTLE_DEPOSIT = 'bottle_deposit_fpt';

    /**
     * @var array
     */
    private $websiteIds;
    /**
     * @var array
     */
    private $bottleDepositFpt;
    /**
     * @var array
     */
    private $bottleDepositFptLabel;
    /**
     * @var ProductInterface
     */
    private $product;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FixedProductTax constructor.
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->logger = $logger;
    }

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * @return float|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBottleDepositFpt()
    {
        $productId = $this->product->getId();
        if (!isset($this->bottleDepositFpt[$productId])
            && ($fptItems = $this->product->getData(self::FPT_BOTTLE_DEPOSIT))) {
            foreach ($fptItems as $fptItem) {
                if ($this->getCurrentWebsiteId() == $fptItem['website_id']) {
                    $this->bottleDepositFpt[$productId] = $this->priceCurrency->format($fptItem['website_value']);
                }
            }
        }

        return isset($this->bottleDepositFpt[$productId]) ? $this->bottleDepositFpt[$productId] : null;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentWebsiteId(): int
    {
        $storeId = $this->storeManager->getStore()->getId();
        if (!isset($this->websiteIds[$storeId])) {
            $this->websiteIds[$storeId] = $this->storeManager->getStore()->getWebsiteId();
        }

        return $this->websiteIds[$storeId];
    }

    /**
     * @return mixed|null
     */
    public function getBottleDepositLabel()
    {
        $productId = $this->product->getId();
        if (!isset($this->bottleDepositFptLabel[$productId])) {
            $this->bottleDepositFptLabel[$productId]
                = $this->product->getResource()->getAttribute(self::FPT_BOTTLE_DEPOSIT)->getStoreLabel();
        }

        return isset($this->bottleDepositFptLabel[$productId]) ? $this->bottleDepositFptLabel[$productId] : null;
    }
}
