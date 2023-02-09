<?php

namespace Coke\PostcodeRestrictions\Ui\Checkout\ConfigProvider;

use Coke\PostcodeRestrictions\Helper\Config;
use Coke\PostcodeRestrictions\Model\ResourceModel\Postcode\CollectionFactory;
use Magento\Checkout\Model\ConfigProviderInterface;

class PostcodeRestrictionsConfigProvider implements ConfigProviderInterface
{
    const IS_VALIDATION_ENABLED = 'postcode_restrictions_validation_enabled';
    const ALLOWED_POSTCODES = 'postcode_restrictions_allowed_postcodes';

    /**
     * @var Config
     */
    private $configHelper;
    /**
     * @var CollectionFactory
     */
    private $postcodesCollectionFactory;

    /**
     * PostcodeRestrictionsConfigProvider constructor.
     * @param Config $configHelper
     * @param CollectionFactory $postcodesCollectionFactory
     */
    public function __construct(
        Config $configHelper,
        CollectionFactory $postcodesCollectionFactory
    )
    {
        $this->configHelper = $configHelper;
        $this->postcodesCollectionFactory = $postcodesCollectionFactory;
    }

    public function getConfig()
    {
        return [
            self::IS_VALIDATION_ENABLED => $this->isValidationEnabled(),
            self::ALLOWED_POSTCODES => $this->getAllowedPostcodes()
        ];
    }

    public function isValidationEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }

    private function getAllowedPostcodes(): array
    {
        return array_column(
            $this->postcodesCollectionFactory->create()
                ->addFieldToFilter('is_active', 1)
                ->getData(),
            'postcode'
        );
    }
}