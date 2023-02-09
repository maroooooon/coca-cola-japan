<?php

namespace FortyFour\Email\Setup\Patch\Data;

use FortyFour\Config\Model\Config;
use FortyFour\Email\Model\ContentUpgrader;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCokeEgyptEmailLogo implements DataPatchInterface
{
    /**
     * @var ContentUpgrader
     */
    private $contentUpgrader;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * AddTopoChicoGrEmailLogo constructor.
     * @param ContentUpgrader $contentUpgrader
     * @param Filesystem $filesystem
     * @param WriterInterface $configWriter
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ContentUpgrader $contentUpgrader,
        Filesystem $filesystem,
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection
    ) {
        $this->contentUpgrader = $contentUpgrader;
        $this->filesystem = $filesystem;
        $this->configWriter = $configWriter;
        $this->resourceConnection = $resourceConnection;
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
     * @return $this|AddCokeEgyptEmailLogo
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function apply()
    {
        $this->removeCurrentLogos();
        $this->installCokeEgyptWebsiteEmailLogo();

        return $this;
    }

    /**
     * @return void
     */
    private function removeCurrentLogos(): void
    {
        $egyptStoreIds = [
            $this->getStoreIdByCode(Config::EGYPT_STORE_CODE),
            $this->getStoreIdByCode(Config::EGYPT_EN_STORE_CODE)
        ];

        foreach ($egyptStoreIds as $egyptStoreId) {
            $this->configWriter->delete(
                'design/email/logo',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $egyptStoreId
            );
        }
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function installCokeEgyptWebsiteEmailLogo()
    {
        $file = 'logo_ar.png';
        $websiteId = $this->getWebsiteIdByCode(Config::EGYPT_WEBSITE_CODE);

        $this->configWriter->save(
            'design/email/logo',
            sprintf('websites/%s/%s', $websiteId, $file),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );

        $mediaDirPath = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath();
        $destination = sprintf('%s/email/logo/websites/%s/%s', $mediaDirPath, $websiteId, $file);

        $this->contentUpgrader->moveFile(
            'images',
            $file,
            $destination
        );
    }

//    /**
//     * @throws \Magento\Framework\Exception\FileSystemException
//     */
//    private function installCokeEgyptEnEmailLogo()
//    {
//        $file = 'logo_eng.png';
//        $cokeEgyptEnStoreId = $this->getStoreIdByCode(Config::EGYPT_EN_STORE_CODE);
//        $this->installEmailLogoForStore($file, $cokeEgyptEnStoreId);
//    }
//
//    /**
//     * @param string $file
//     * @param int $storeId
//     * @throws \Magento\Framework\Exception\FileSystemException
//     */
//    private function installEmailLogoForStore(string $file, int $storeId): void
//    {
//        $this->configWriter->save(
//            'design/email/logo',
//            sprintf('stores/%s/%s', $storeId, $file),
//            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
//            $storeId
//        );
//
//        $mediaDirPath = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath();
//        $destination = sprintf('%s/email/logo/stores/%s/%s', $mediaDirPath, $storeId, $file);
//
//        $this->contentUpgrader->moveFile(
//            'images',
//            $file,
//            $destination
//        );
//    }

    /**
     * @param string $storeCode
     * @return string
     */
    private function getStoreIdByCode(string $storeCode): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store'),
            'store_id'
        )->where("code = ?", $storeCode);

        return $connection->fetchOne($select);
    }

    /**
     * @param string $code
     * @return string
     */
    private function getWebsiteIdByCode(string $code): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store_website'),
            'website_id'
        )->where("code = ?", $code);

        return $connection->fetchOne($select);
    }
}
