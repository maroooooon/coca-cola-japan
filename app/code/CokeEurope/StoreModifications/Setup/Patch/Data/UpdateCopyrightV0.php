<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use CokeEurope\StoreModifications\Helper\Data;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;

class UpdateCopyrightV0 implements DataPatchInterface
{
    const COPYRIGHT_PATH = 'design/footer/copyright';
    const COPYRIGHT_MESSAGE_EU = "For your health, avoid snacking between meals - www.mangerbouger.fr © 2021 The Coca‐Cola Company. Coca-Cola, Coca‐Cola light, Coca-Cola zero sugars, Coca‐Cola life, Saving the moment and the Contour Bottle are registered trademarks of The Coca‐Cola Company";

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param WriterInterface $configWriter
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param Data $helper
     */
    public function __construct(
        WriterInterface $configWriter,
        WebsiteRepositoryInterface $websiteRepository,
        Data $helper
    ) {
        $this->configWriter = $configWriter;
        $this->websiteRepository = $websiteRepository;
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

    public function apply()
    {
        $this->configWriter->save(
            self::COPYRIGHT_PATH,
            self::COPYRIGHT_MESSAGE_EU,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $this->helper->getEuropeWebsite()->getId()
        );

        $this->configWriter->save(
            self::COPYRIGHT_PATH,
            self::COPYRIGHT_MESSAGE_EU,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $this->helper->getUkWebsite()->getId()
        );
    }

}
