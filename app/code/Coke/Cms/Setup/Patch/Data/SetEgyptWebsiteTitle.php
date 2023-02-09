<?php

namespace Coke\Cms\Setup\Patch\Data;

use Coke\Cms\Helper\Data;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;

class SetEgyptWebsiteTitle implements DataPatchInterface
{
    const XML_PATH_DESIGN_HEAD_TITLE = 'design/head/default_title';
    const XML_PATH_DESIGN_HEAD_TITLE_SUFFIX = 'design/head/title_suffix';

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

    /**
     * @return $this|SetEgyptWebsiteTitle
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $website = $this->helper->getEgyptWebsite();

        $this->config->saveConfig(
            self::XML_PATH_DESIGN_HEAD_TITLE,
            'Coca-Cola Delivery',
            ScopeInterface::SCOPE_WEBSITES,
            $website->getId()
        );

        $this->config->saveConfig(
            self::XML_PATH_DESIGN_HEAD_TITLE_SUFFIX,
            '| Coca-Cola Delivery',
            ScopeInterface::SCOPE_WEBSITES,
            $website->getId()
        );

        return $this;
    }
}
