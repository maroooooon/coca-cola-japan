<?php

namespace Coke\Cms\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class SetTurkishWebsiteContactEmails implements DataPatchInterface
{
    private static $xmlPathEmailAddresses = [
        'trans_email/ident_sales/email',
        'trans_email/ident_general/email',
        'trans_email/ident_support/email',
        'trans_email/ident_custom1/email'
    ];

    /**
     * @var Config
     */
    private $config;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * SetTurkishWebsiteContactEmails constructor.
     * @param Config $config
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        Config $config,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->config = $config;
        $this->websiteRepository = $websiteRepository;
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
        $turkishWebsite = $this->websiteRepository->get('olnb_turkey');

        foreach (self::$xmlPathEmailAddresses as $xmlPathEmailAddress) {
            $this->config->saveConfig(
                $xmlPathEmailAddress,
                'iletisimmerkezi@coca-cola.com',
                ScopeInterface::SCOPE_WEBSITES,
                $turkishWebsite->getId()
            );
        }

        return $this;
    }
}
