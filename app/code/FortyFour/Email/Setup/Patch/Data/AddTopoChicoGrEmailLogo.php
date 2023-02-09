<?php

namespace FortyFour\Email\Setup\Patch\Data;

use FortyFour\Email\Model\ContentUpgrader;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddTopoChicoGrEmailLogo implements DataPatchInterface
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
     * @return $this|AddTopoChicoGrEmailLogo
     */
    public function apply()
    {
        $this->installTopoChicoGrEmailLogo();
        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function installTopoChicoGrEmailLogo()
    {
        $file = 'topo_chico_gr_logo.png';
        $websiteId = $this->getTopoChicoGrWebsiteId();

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

    /**
     * @return string
     */
    private function getTopoChicoGrWebsiteId(): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('store_website'),
            'website_id'
        )->where("code = 'topo_chico_gr_website'");

        return $connection->fetchOne($select);
    }
}
