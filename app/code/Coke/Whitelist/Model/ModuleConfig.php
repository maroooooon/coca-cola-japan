<?php

namespace Coke\Whitelist\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ModuleConfig
{
    const XML_PATH_IS_ENABLED = 'coke_whitelist/general/enabled';
    const XML_PATH_IS_RESTRICTION_ENABLED = 'coke_whitelist/general/is_restriction_enabled';
    const XML_PATH_SHOW_WHITELIST_ITEM_STATUS = 'coke_whitelist/general/show_whitelist_item_status';
    const XML_PATH_SHOW_REVIEW_DISCLAIMER = 'coke_whitelist/general/show_whitelist_review_disclaimer';
    const XML_PATH_IS_NAMES_ENABLED = 'coke_whitelist/general/is_names_enabled';
    const XML_PATH_IMAGE_THRESHOLD= 'coke_whitelist/general/image_threshold';
    const XML_PATH_UPDATE_TO_FROM_ON_IMAGE = 'coke_whitelist/general/update_to_from_on_image';
    const XML_PATH_ILLEGAL_CHARACTERS = 'coke_whitelist/general/illegal_characters';
    const XML_PATH_ORDER_STATUS = 'coke_whitelist/general/pending_whitelist_item_order_status';
    const XML_PATH_APPROVED_ORDER_STATUS = 'coke_whitelist/general/approved_whitelist_item_order_status';
    const XML_PATH_DENIED_ORDER_STATUS = 'coke_whitelist/general/denied_whitelist_item_order_status';

    const XML_PATH_CANCEL_DENIED_ORDER = 'coke_whitelist/denied_order/cancel_denied_order_enabled';


    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @return bool
     */
    public function isRestrictionEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_IS_RESTRICTION_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canShowWhitelistItemStatus($store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_WHITELIST_ITEM_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canShowWhitelistReviewDisclaimer($store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SHOW_REVIEW_DISCLAIMER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isNamesEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_IS_NAMES_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }
    /**
     * @return bool
     */
    public function getImageThreshold()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_IMAGE_THRESHOLD, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function canUpdateToAndFromOnImage($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_UPDATE_TO_FROM_ON_IMAGE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getIllegalCharacters(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_ILLEGAL_CHARACTERS, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getPendingWhitelistItemOrderStatus($store = null): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_ORDER_STATUS, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getApprovedWhitelistItemOrderStatus($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_APPROVED_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getDeniedWhitelistItemOrderStatus($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_DENIED_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        ) ?: 'denied';
    }

    /**
     * @param null $store
     * @return string
     */
    public function isCancelDeniedOrderEnabled($store = null): string
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CANCEL_DENIED_ORDER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string|null
     */
    public function getEnglishMaxLength($store = null): ?string
    {
        return (string) $this->scopeConfig->getValue(
            'coke_whitelist/input_validation/english_max_length',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string|null
     */
    public function getJapaneseMaxLength($store = null): ?string
    {
        return (string) $this->scopeConfig->getValue(
            'coke_whitelist/input_validation/japanese_max_length',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
