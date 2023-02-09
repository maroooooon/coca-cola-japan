<?php

namespace FortyFour\Email\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir;

class ContentUpgrader
{
    const THIS_MODULE = 'FortyFour_Email';
    const CONTENT_FOLDER = 'Setup/content';

    /**
     * @var string
     */
    protected $contentDirectory;
    /**
     * @var Dir
     */
    protected $dir;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * ContentUpgrader constructor.
     * @param Dir $dir
     * @param Filesystem $filesystem
     */
    public function __construct(
        Dir $dir,
        Filesystem $filesystem
    ) {
        $this->dir = $dir;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    protected function getContentDirectory(): string
    {
        if (!$this->contentDirectory) {
            $this->contentDirectory = $this->getModuleDirectory(self::THIS_MODULE)
                . '/' . self::CONTENT_FOLDER;
        }

        return $this->contentDirectory;
    }

    /**
     * @param string $moduleName
     * @return string
     */
    private function getModuleDirectory(string $moduleName): string
    {
        try {
            $path = $this->dir->getDir($moduleName);
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException(sprintf(
                'Unable to find module directory path for %s. Exception thrown: %s %s',
                $moduleName,
                $e->getMessage(),
                $e->getTraceAsString()
            ));
        }

        return rtrim($path, '/');
    }

    /**
     * @param string $type
     * @param string $identifier
     * @param string $destination
     * @return false|string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function moveFile(string $type, string $identifier, string $destination)
    {
        $filePath = sprintf(
            '%s/%s/%s',
            rtrim($this->getContentDirectory(), '/'),
            $type,
            $identifier
        );

        $directoryWrite = $this->filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::ROOT
        );

        $directoryWrite->copyFile(
            $filePath,
            $destination
        );
    }

    /**
     * @param string $type
     * @param string $identifier
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function readFile(string $type, string $identifier)
    {
        $filePath = sprintf(
            '%s/%s/%s',
            rtrim($this->getContentDirectory(), '/'),
            $type,
            $identifier
        );

        return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)
            ->readFile($filePath);
    }
}
