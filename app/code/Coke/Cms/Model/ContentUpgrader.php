<?php

namespace Coke\Cms\Model;

use Magento\Framework\Module\Dir;
use Coke\Cms\Model\Content\Page;
use Coke\Cms\Model\Content\Block;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Cms\Model\PageFactory;

class ContentUpgrader
{
    const THIS_MODULE = 'Coke_Cms';
    const CONTENT_FOLDER = 'Setup/content';
    const CONTENT_EXT = '.html';
    /**
     * @var string
     */
    protected $contentDirectory;
    /**
     * @var Dir
     */
    protected $dir;
    /**
     * @var Page
     */
    protected $pageContent;
    /**
     * @var Block
     */
    protected $blockContent;
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;
    /**
     * @var CmsPageCollectionFactory
     */
    private $pageCollectionFactory;
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * ContentUpgrader constructor.
     * @param Dir $dir
     * @param Page $pageContent
     * @param Block $blockContent
     * @param PageRepositoryInterface $pageRepository
     * @param CmsPageCollectionFactory $pageCollectionFactory
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Dir $dir,
        Page $pageContent,
        Block $blockContent,
        PageRepositoryInterface $pageRepository,
        CmsPageCollectionFactory $pageCollectionFactory,
        PageFactory $pageFactory
    ) {
        $this->dir = $dir;
        $this->pageContent = $pageContent;
        $this->blockContent = $blockContent;
        $this->pageRepository = $pageRepository;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return string
     */
    protected function getContentDirectory(): string
    {
        if (!$this->contentDirectory) {
            $this->contentDirectory = $this->getModuleDirectory(self::THIS_MODULE) . '/' .
                self::CONTENT_FOLDER;
        }

        return $this->contentDirectory;
    }

    /**
     * @param array $identifiers
     * @param callable|null $contentTransformer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgradePages(array $identifiers, ?callable $contentTransformer = null): array
    {
        $identifiers = $this->normalizeIdentifiers($identifiers);

        $pages = [];
        foreach ($identifiers as $identifier => $data) {
            $content = $this->getContentFile('pages', $identifier);

            if ($contentTransformer !== null) {
                $content = $contentTransformer($content);
            }

            $pages[] = $this->pageContent->applyChanges(
                $identifier,
                $content,
                $data
            );
        }

        return $pages;
    }

    /**
     * @param array $identifiers
     * @param callable|null $contentTransformer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgradePagesByStoreView(array $identifiers, ?callable $contentTransformer = null): array
    {
        $identifiers = $this->normalizeIdentifiers($identifiers);

        $pages = [];
        foreach ($identifiers as $identifier => $data) {
            $content = $this->getContentFile('pages', $identifier);

            if ($contentTransformer !== null) {
                $content = $contentTransformer($content);
            }

            $pages[] = $this->pageContent->applyChangesByStoreId(
                $data['identifier'],
                $content,
                $data
            );
        }

        return $pages;
    }

    /**
     * @param array $identifiers
     * @param callable|null $contentTransformer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgradeBlocks(array $identifiers, ?callable $contentTransformer = null): array
    {
        $identifiers = $this->normalizeIdentifiers($identifiers);

        $blocks = [];
        foreach ($identifiers as $identifier => $data) {
            $content = $this->getContentFile('blocks', $identifier);

            if ($contentTransformer !== null) {
                $content = $contentTransformer($content);
            }

            $blocks[] = $this->blockContent->applyChanges(
                $identifier,
                $content,
                $data
            );
        }

        return $blocks;
    }

    /**
     * @param array $identifiers
     * @param callable|null $contentTransformer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgradeBlocksByStoreView(array $identifiers, ?callable $contentTransformer = null): array
    {
        $identifiers = $this->normalizeIdentifiers($identifiers);

        $blocks = [];
        foreach ($identifiers as $identifier => $data) {
            $content = $this->getContentFile('blocks', $identifier);

            if ($contentTransformer !== null) {
                $content = $contentTransformer($content);
            }

            $blocks[] = $this->blockContent->applyChangesByStoreId(
                $data['identifier'],
                $content,
                $data
            );
        }

        return $blocks;
    }

    /**
     * @param array $identifiers
     * @return array
     */
    private function normalizeIdentifiers(array $identifiers): array
    {
        $result = [];

        foreach ($identifiers as $i => $data) {
            if (is_array($data)) {
                $result[$i] = $data;
            } else {
                $result[$data] = [];
            }
        }

        return $result;
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
     * @return string
     */
    public function getContentFile(string $type, string $identifier): string
    {
        $filePath = sprintf(
            '%s/%s/%s%s',
            rtrim($this->getContentDirectory(), '/'),
            $type,
            $identifier,
            self::CONTENT_EXT
        );

        // @phpcs:ignore
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('Content file does not exist: ' . $filePath);
        }

        // @phpcs:ignore
        if (($content = file_get_contents($filePath)) === false) {
            throw new \InvalidArgumentException('Unable to read content file: ' . $filePath);
        }

        return $content;
    }
}
