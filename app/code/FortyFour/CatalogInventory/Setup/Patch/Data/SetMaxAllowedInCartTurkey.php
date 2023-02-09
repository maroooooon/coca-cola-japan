<?php

namespace FortyFour\CatalogInventory\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;

class SetMaxAllowedInCartTurkey implements DataPatchInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * AddTopoChicoEmailAddresses constructor.
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
     * @return $this|SetMaxAllowedInCartTurkey
     * @throws NoSuchEntityException
     */
    public function apply()
    {
        $turkeyWebsite = $this->getTurkeyWebsite();
        $this->configWriter->save(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_MAX_SALE_QTY,
            1,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $turkeyWebsite->getId()
        );

        return $this;
    }

    /**
     * @return WebsiteInterface
     * @throws NoSuchEntityException
     */
    private function getTurkeyWebsite(): WebsiteInterface
    {
        return $this->websiteRepository->get('olnb_turkey');
    }
}
