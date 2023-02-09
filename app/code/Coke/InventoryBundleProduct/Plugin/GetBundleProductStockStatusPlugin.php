<?php

namespace Coke\InventoryBundleProduct\Plugin;

use Coke\InventoryBundleProduct\Model\Attribute;
use Magento\Bundle\Api\Data\OptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryBundleProduct\Model\GetBundleProductStockStatus;
use Magento\InventoryBundleProduct\Model\GetProductSelection;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventorySalesApi\Api\AreProductsSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyRequestInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyResultInterface;
use Psr\Log\LoggerInterface;

class GetBundleProductStockStatusPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var GetProductSelection
     */
    private $getProductSelection;
    /**
     * @var AreProductsSalableForRequestedQtyInterface
     */
    private $areProductsSalableForRequestedQty;
    /**
     * @var IsProductSalableForRequestedQtyRequestInterfaceFactory
     */
    private $isProductSalableForRequestedQtyRequestFactory;
    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;


    /**
     * @param LoggerInterface $logger
     * @param GetProductSelection $getProductSelection
     * @param AreProductsSalableForRequestedQtyInterface $areProductsSalableForRequestedQty
     * @param IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     */
    public function __construct(
        LoggerInterface $logger,
        GetProductSelection $getProductSelection,
        AreProductsSalableForRequestedQtyInterface $areProductsSalableForRequestedQty,
        IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory,
        GetStockItemConfigurationInterface $getStockItemConfiguration
    ) {
        $this->logger = $logger;
        $this->getProductSelection = $getProductSelection;
        $this->areProductsSalableForRequestedQty = $areProductsSalableForRequestedQty;
        $this->isProductSalableForRequestedQtyRequestFactory = $isProductSalableForRequestedQtyRequestFactory;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
    }

    /**
     * @param GetBundleProductStockStatus $subject
     * @param callable $proceed
     * @param ProductInterface $product
     * @param array $bundleOptions
     * @param int $stockId
     * @return mixed
     */
    public function aroundExecute(
        GetBundleProductStockStatus $subject,
        callable $proceed,
        ProductInterface $product,
        array $bundleOptions,
        int $stockId
    ) {
        if (!$product->getData(Attribute::REQUIRE_ALL_BUNDLE_OPTIONS)) {
            return $proceed($product, $bundleOptions, $stockId);
        }

        try {
            return $this->execute($product, $bundleOptions, $stockId);
        } catch (SkuIsNotAssignedToStockException | LocalizedException $e) {
            $this->logger->info(__('[GetBundleProductStockStatusPlugin] %1', $e->getMessage()));
        }

        return false;
    }

    /**
     * @param ProductInterface $product
     * @param array $bundleOptions
     * @param int $stockId
     * @return bool
     * @throws LocalizedException
     * @throws SkuIsNotAssignedToStockException
     */
    private function execute(ProductInterface $product, array $bundleOptions, int $stockId): bool
    {
        $stockItemConfiguration = $this->getStockItemConfiguration->execute($product->getDataByKey('sku'), $stockId);
        if (!$stockItemConfiguration->getExtensionAttributes()->getIsInStock()) {
            return false;
        }
        $isSalable = true;
        foreach ($bundleOptions as $option) {
            $results = $this->getAreSalableSelections($product, $option, $stockId);
            foreach ($results as $result) {
                $this->logger->debug(__('Result is salable: %1', $result->isSalable()));
                if (!$result->isSalable()) {
                    $isSalable = false;
                    break;
                }
            }
        }

        $product->setData('is_salable', $isSalable);
        $product->setData('all_items_salable', $isSalable);

        return $isSalable;
    }

    /**
     * Get bundle product selection qty.
     *
     * @param Product $product
     * @param int $stockId
     * @return float
     * @throws LocalizedException
     * @throws SkuIsNotAssignedToStockException
     */
    private function getRequestedQty(Product $product, int $stockId): float
    {
        if ((int)$product->getSelectionCanChangeQty()) {
            $stockItemConfiguration = $this->getStockItemConfiguration->execute((string)$product->getSku(), $stockId);
            return $stockItemConfiguration->getMinSaleQty();
        }

        return (float)$product->getSelectionQty();
    }

    /**
     * Get are bundle product selections salable.
     *
     * @param ProductInterface $product
     * @param OptionInterface $option
     * @param int $stockId
     * @return IsProductSalableForRequestedQtyResultInterface[]
     * @throws LocalizedException
     * @throws SkuIsNotAssignedToStockException
     */
    private function getAreSalableSelections(ProductInterface $product, OptionInterface $option, int $stockId): array
    {
        $bundleSelections = $this->getProductSelection->execute($product, $option);
        $skuRequests = [];
        foreach ($bundleSelections->getItems() as $selection) {
            if ((int)$selection->getStatus() === Status::STATUS_ENABLED) {
                $skuRequests[] = $this->isProductSalableForRequestedQtyRequestFactory->create(
                    [
                        'sku' => (string)$selection->getSku(),
                        'qty' => $this->getRequestedQty($selection, $stockId),
                    ]
                );
            }
        }

        return $this->areProductsSalableForRequestedQty->execute($skuRequests, $stockId);
    }
}
