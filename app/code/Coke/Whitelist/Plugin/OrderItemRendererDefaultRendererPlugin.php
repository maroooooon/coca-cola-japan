<?php

namespace Coke\Whitelist\Plugin;

use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\Source\Status;
use Coke\Whitelist\Model\WhiteListHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class OrderItemRendererDefaultRendererPlugin
{
    const ORDER_ITEM_LAYOUT_NAME = 'sales.order.items.renderers.default';

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
     * @param \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject
     * @param $template
     * @return array
     */
    public function beforeSetTemplate(\Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject, $template)
    {
        if (!$this->config->canShowWhitelistItemStatus() || $subject->getNameInLayout() != self::ORDER_ITEM_LAYOUT_NAME) {
            return [$template];
        }

        $template = "Coke_Whitelist::order/items/renderer/default.phtml";
        return [$template];
    }

    /**
     * @param \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject
     * @param $result
     */
    public function afterGetItemOptions(\Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject, $result)
    {
        if (!$this->config->canShowWhitelistItemStatus()) {
            return $result;
        }

        foreach ($result as $key => $option) {
            if ((isset($option['option_id'], $option['value'], $option['option_type']))
                && strpos($option['option_type'], 'whitelist') !== false) {
                if ($this->getWhitelistValueStatus($option['option_id'], $option['value']) == Status::PENDING) {
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
