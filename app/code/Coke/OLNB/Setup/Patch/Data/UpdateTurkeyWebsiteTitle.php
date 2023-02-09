<?php

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class UpdateTurkeyWebsiteTitle implements DataPatchInterface
{
    const TURKEY_WEBSITE_TITLE = 'Dahaiyisiicin';

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @param WriterInterface $configWriter
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        WriterInterface $configWriter,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->configWriter = $configWriter;
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
     * @return $this|UpdateTurkeyWebsiteTitle
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply()
    {
        $turkeyWebsite = $this->websiteRepository->get('olnb_turkey');
        $this->configWriter->save(
            'design/head/default_title',
            self::TURKEY_WEBSITE_TITLE,
            ScopeInterface::SCOPE_WEBSITES,
            $turkeyWebsite->getId()
        );

        $this->configWriter->save(
            'design/head/title_suffix',
            ' | ' . self::TURKEY_WEBSITE_TITLE,
            ScopeInterface::SCOPE_WEBSITES,
            $turkeyWebsite->getId()
        );

        return $this;
    }
}
