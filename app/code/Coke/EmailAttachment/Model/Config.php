<?php

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 */
class Config
{
    const XML_PATH_INVOICE_IS_ENABLED = 'coke_email_attachment/invoice/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }


    /**
     * @param int|null $storeId
     * @return bool
     */
    public function invoiceAttachmentIsEnabled($storeId = null): bool
    {
        $scopeCode = null;

        try {
            if ($storeId) {
                $scopeCode = $this->storeManager->getStore($storeId)->getWebsiteId();
            }
        } catch (NoSuchEntityException $e) {
            // do nothing
        }

        return (bool)$this->scopeConfig
            ->getValue(self::XML_PATH_INVOICE_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE, $scopeCode);
    }
}
