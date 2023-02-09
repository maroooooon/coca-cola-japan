<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ChangeCopyrightFieldValueTurkishStore
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class ChangeCopyrightFieldValueTurkishStore implements DataPatchInterface
{
    /** @var string Footer message for Turkish store */
    const COPYRIGHT_MESSAGE = "© 2021 The Coca-Cola Company, tüm hakları saklıdır";

    /**
     * @var string
     */
    private $olnbWebsiteCode = 'olnb_turkey';

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * ChangeCopyrightFieldValueTurkishStore constructor.
     *
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
    public static function getDependencies(): array
    {
        return [AddCopyrightFieldValue::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Apply patch
     *
     * @return $this|AddCopyrightFieldValue
     * @throws NoSuchEntityException
     */
    public function apply(): DataPatchInterface
    {
        $this->saveCopyrightFieldByWebsite($this->websiteRepository->get($this->olnbWebsiteCode));

        return $this;
    }

    /**
     * Save config value
     *
     * @param WebsiteInterface $website
     */
    private function saveCopyrightFieldByWebsite(WebsiteInterface $website): void
    {
        $this->configWriter->save(
           AddCopyrightFieldValue::COPYRIGHT_MESSAGE_PATH,
            self::COPYRIGHT_MESSAGE,
            ScopeInterface::SCOPE_WEBSITES,
            $website->getId()
        );
    }
}
