<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;

class SetArabicFaqTitle implements DataPatchInterface
{
    /**
     * @var string
     */
    const XML_PATH_FAQ_TITLE = 'coke_faq/faq_settings/title';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Config $config
     * @param Data $helper
     */
    public function __construct(
        Config $config,
        Data $helper
    ) {
        $this->config = $config;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $store = $this->helper->getEgyptArabicStore();

        $this->config->saveConfig(
            self::XML_PATH_FAQ_TITLE,
            'الأسئلة الشائعة',
            ScopeInterface::SCOPE_STORES,
            $store->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
