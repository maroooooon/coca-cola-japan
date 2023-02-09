<?php

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class AddCopyrightFieldValue implements DataPatchInterface
{
    const COPYRIGHT_MESSAGE_PATH = 'design/footer/copyright';
    const COPYRIGHT_MESSAGE = "© 2021, The Coca-Cola Company. All rights reserved. Coca-Cola is a registered trademark of The Coca-Cola Company";

    /**
     * @var string[]
     */
    private static $olnbWebsiteCodes = ['olnb_gb', 'olnb_eu', 'olnb_norway', 'olnb_turkey', 'olnb_ni'];

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
     * @return $this|AddCopyrightFieldValue
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply()
    {
        foreach (self::$olnbWebsiteCodes as $olnbWebsiteCode) {
            $this->saveCopyrightFieldByWebsite($this->websiteRepository->get($olnbWebsiteCode));
        }

        return $this;
    }

    /**
     * @param \Magento\Store\Api\Data\WebsiteInterface $website
     * @return void
     */
    private function saveCopyrightFieldByWebsite(\Magento\Store\Api\Data\WebsiteInterface $website): void
    {
        $this->configWriter->save(
            self::COPYRIGHT_MESSAGE_PATH,
            self::COPYRIGHT_MESSAGE,
            ScopeInterface::SCOPE_WEBSITES,
            $website->getId()
        );
    }
}
