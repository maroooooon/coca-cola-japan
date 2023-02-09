<?php

namespace Coke\Whitelist\Plugin;

use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Coke\Whitelist\Model\WhitelistRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Coke\Whitelist\Model\WhiteListHelper;

class ValidateWhitelistOptionsBeforeAddToCartPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CollectionFactory
     */
    private $whitelistCollectionFactory;
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    private $whitelistHelper;
    /**
     * @var WhitelistRepository
     */
    private $whitelistRepository;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * ValidateWhitelistOptionsBeforeAddToCartPlugin constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $whitelistCollectionFactory
     * @param ModuleConfig $moduleConfig
     * @param WhiteListHelper $whitelistHelper
     * @param WhitelistRepository $whitelistRepository
     * @param Session $checkoutSession
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        CollectionFactory $whitelistCollectionFactory,
        ModuleConfig $moduleConfig,
        WhiteListHelper $whitelistHelper,
        WhitelistRepository $whitelistRepository,
        Session $checkoutSession
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->whitelistCollectionFactory = $whitelistCollectionFactory;
        $this->moduleConfig = $moduleConfig;
        $this->whitelistHelper = $whitelistHelper;
        $this->whitelistRepository = $whitelistRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param $subject
     * @param $productInfo
     * @param null $requestInfo
     * @throws LocalizedException
     */
    public function beforeAddProduct(\Magento\Checkout\Model\Cart $subject, $productInfo, $requestInfo = null)
    {
        if(!$this->moduleConfig->isEnabled() || !$this->moduleConfig->isRestrictionEnabled()) {
            return;
        }

        try {
            if (!isset($requestInfo['options'])) {
                return;
            }

            $storeId = $this->storeManager->getStore()->getId();
            $isPending = false;

            $product = $this->_getProduct($productInfo);

            foreach ($product->getOptions() as $productOption) {
                if ($productOption->getWhitelistTypeId() &&
                    isset($requestInfo['options'][$productOption->getOptionId()]) &&
                    strlen($requestInfo['options'][$productOption->getOptionId()]) > 0) {

                    if (!$productOption->getAllowNonWhitelistedValues()) {
                        $requestOptionValue = $this->stripReturns($requestInfo['options'][$productOption->getOptionId()]);
                        if (!$this->whitelistRepository->isValueApproved($productOption->getWhitelistTypeId(), $requestOptionValue, $storeId)) {
                            throw new \Exception("Please select an approved value from the lists provided.");
                        }
                    } else if ($productOption->getAllowNonWhitelistedValues() && $productOption->getRequireNonWhitelistedValueApproval()) {
                        if ($this->whitelistHelper->isTextDenied($productOption->getWhitelistTypeId(), $requestInfo['options'][$productOption->getOptionId()])) {
                            throw new \Exception(__("The text chosen %1 is not allowed.", $requestInfo['options'][$productOption->getOptionId()]));
                        }
                    }

                    $this->whitelistHelper->validateMaxLength($requestInfo['options'][$productOption->getOptionId()]);

                    if ($this->moduleConfig->canShowWhitelistReviewDisclaimer() &&
                        !$this->whitelistHelper->getWhitelistValueStatus(
                            $productOption->getWhitelistTypeId(),
                            $requestInfo['options'][$productOption->getOptionId()],
                            $storeId
                        )
                    ) {
                        $isPending = true;
                    }
                }
            }

            if ($isPending) {
                $this->checkoutSession->getQuote()->setData('whitelist_status_pending', 1);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    private function getWhitelistNames($whiteListTypeId)
    {
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFieldToSelect(['value'])
            ->addFilter('type_id', $whiteListTypeId)
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id',  $this->storeManager->getStore()->getId());

        return $collection->load()->getColumnValues('value');
    }

    protected function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again.")
                );
            }
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again."),
                    $e
                );
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
        if (!is_array($product->getWebsiteIds()) || !in_array($currentWebsiteId, $product->getWebsiteIds())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        return $product;
    }

    /**
     * @param $value
     * @return string|string[]|null
     */
    private function stripReturns($value)
    {
        return preg_replace('/\r|\n/', '', $value);
    }
}
