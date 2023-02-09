<?php

namespace Coke\Whitelist\Plugin;

use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\WhiteListHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ProductConfigurationPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ModuleConfig
     */
    private $config;
    /**
     * @var WhiteListHelper
     */
    private $whiteListHelper;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param LoggerInterface $logger
     * @param ModuleConfig $config
     * @param WhiteListHelper $whiteListHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        ModuleConfig $config,
        WhiteListHelper $whiteListHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->whiteListHelper = $whiteListHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Catalog\Helper\Product\Configuration $subject
     * @param $result
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return mixed
     */
    public function afterGetCustomOptions(
        \Magento\Catalog\Helper\Product\Configuration $subject,
        $result,
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
    ) {
        $product = $item->getProduct();
        $productType = $product->getTypeId();
        if (!$this->config->canShowWhitelistItemStatus() || $productType == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $result;
        }

        foreach ($result as $key => $option) {
            if (isset($option['option_id'], $option['value'])) {
                if (!$this->getWhitelistValueStatus($option['option_id'], $option['value'])) {
                    $option['whitelist_status_pending'] = '1';
                }
                $result[$key] = $option;
            }
        }
        return $result;
    }

    /**
     * @param int $optionId
     * @param string $value
     * @return string|null
     */
    private function getWhitelistValueStatus(int $optionId, string $value): ?string
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $whitelistTypeId = $this->whiteListHelper->getWhitelistTypeIdFromOptionId($optionId);
            return $this->whiteListHelper->getWhitelistValueStatus(
                $whitelistTypeId,
                $value,
                $storeId
            );
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
