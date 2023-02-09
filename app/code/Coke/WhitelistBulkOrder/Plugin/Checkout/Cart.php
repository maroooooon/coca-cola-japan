<?php

/**
 * @category Bounteous
 * @copyright Copyright (c) 2021 Bounteous LLC
 */

declare(strict_types=1);

namespace Coke\WhitelistBulkOrder\Plugin\Checkout;

use Closure;
use Coke\France\Helper\Config as CokeFranceConfig;
use Coke\WhitelistBulkOrder\Model\Config as ModuleConfig;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Checkout\Model\Cart as MagentoCart;
use Magento\Checkout\Model\ResourceModel\Cart as ResourceModelCart;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Coke\Whitelist\Model\WhiteListHelper;

/**
 * Class Cart
 */
class Cart extends MagentoCart
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var CokeFranceConfig
     */
    private $cokeFranceConfig;


    private $whitelistHelper;

    /**
     * Cart constructor.
     * @param ManagerInterface $eventManager
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ResourceModelCart $resourceCart
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     * @param MessageManagerInterface $messageManager
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateInterface $stockState
     * @param CartRepositoryInterface $quoteRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ModuleConfig $moduleConfig
     * @param CokeFranceConfig $cokeFranceConfig
     * @param WhiteListHelper $whitelistHelper
     * @param array $data
     */
    public function __construct(
        ManagerInterface $eventManager,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ResourceModelCart $resourceCart,
        Session $checkoutSession,
        CustomerSession $customerSession,
        MessageManagerInterface $messageManager,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        CartRepositoryInterface $quoteRepository,
        ProductRepositoryInterface $productRepository,
        ModuleConfig $moduleConfig,
        CokeFranceConfig $cokeFranceConfig,
        WhiteListHelper $whitelistHelper,
        array $data = []
    ) {
        parent::__construct(
            $eventManager,
            $scopeConfig,
            $storeManager,
            $resourceCart,
            $checkoutSession,
            $customerSession,
            $messageManager,
            $stockRegistry,
            $stockState,
            $quoteRepository,
            $productRepository,
            $data
        );
        $this->moduleConfig = $moduleConfig;
        $this->cokeFranceConfig = $cokeFranceConfig;
        $this->whitelistHelper = $whitelistHelper;
    }

    /**
     * @param MagentoCart $subject
     * @param Closure $proceed
     * @param mixed $productInfo
     * @param null $requestInfo
     * @return mixed
     * @throws LocalizedException
     *
     * Checking params implemented in the plugin in the 'beforeAddProduct' function
     * @see \Coke\Whitelist\Plugin\ValidateWhitelistOptionsBeforeAddToCartPlugin::beforeAddProduct
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @noinspection PhpUnusedParameterInspection
     */
    public function aroundAddProduct(
        MagentoCart $subject,
        Closure $proceed,
        $productInfo,
        $requestInfo = null
    )
    {
        if ($this->moduleConfig->isEnabled()) {
            $product = $this->_getProduct($productInfo);
            $productId = $product->getId();

            if ($productId && $this->isAllowedBulkOrders($product)) {
                foreach ($product->getOptions() as $productOption) {
                    if (!$this->isAllowedBulkOrderByOptions($requestInfo, $productOption)) {
                        continue;
                    }

                    /**
                     * @todo Separator via Admin UI
                     */
                    /** @noinspection PhpSingleStatementWithBracesInspection */
                    $requestOptions = preg_split("/\r|\n/", (string) $requestInfo['options'][$productOption->getOptionId()], -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($requestOptions as $optionValue) {
                        if ($this->whitelistHelper->isTextDenied($productOption->getWhitelistTypeId(), $optionValue) ||
                            !$this->whitelistHelper->validateText($optionValue)
                        ) {
                            $this->messageManager->addNoticeMessage(__("Le type de condition {$optionValue} n’est pas autorisé"));
                            continue;
                        }

                        $productForQuote = clone $product;

                        $requestInfo['options'][$productOption->getOptionId()] = trim($optionValue);
                        $request = $this->_getProductRequest($requestInfo);

                        $this->getQuote()->addProduct($productForQuote, $request);
                    }
                }

                return $this;
            }
        }

        return $proceed($productInfo, $requestInfo);
    }

    /**
     * @param string $value
     * @return string|string[]|null
     */
    private function stripReturns(string $value)
    {
        /** @noinspection RegExpSingleCharAlternation */
        return (string)preg_replace('/\r|\n/', '', $value);
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function isAllowedBulkOrders(Product $product): bool
    {
        if (!$this->cokeFranceConfig->isEnabled() ||
            !$bulkBottleSku = $this->cokeFranceConfig->bulkBottleSku()) {
            return false;
        }

        return stripos($product->getSku(), $bulkBottleSku) === 0;
    }

    /**
     * @param array $requestInfo
     * @param Option $productOption
     * @return bool
     * @noinspection PhpUndefinedMethodInspection
     */
    private function isAllowedBulkOrderByOptions(array $requestInfo, Option $productOption): bool
    {
        if ($productOption->getWhitelistTypeId() &&
            isset($requestInfo['options'][$productOption->getOptionId()]) &&
            strlen($requestInfo['options'][$productOption->getOptionId()]) > 0) {

            return true;
        }

        return false;
    }
}
