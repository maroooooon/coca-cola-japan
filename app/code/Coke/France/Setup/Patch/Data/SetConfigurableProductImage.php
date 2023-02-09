<?php

namespace Coke\France\Setup\Patch\Data;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail;
use Magento\ConfigurableProduct\Model\Product\Configuration\Item\ItemProductResolver;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class SetConfigurableProductImage implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * SetConfigurableProductImage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param WriterInterface $configWriter
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        WriterInterface $configWriter,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configWriter = $configWriter;
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->configWriter->save(
            ItemProductResolver::CONFIG_THUMBNAIL_SOURCE,
            Thumbnail::OPTION_USE_OWN_IMAGE,
            ScopeInterface::SCOPE_WEBSITES,
            $this->websiteRepository->get('france_d2c')->getId()
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
