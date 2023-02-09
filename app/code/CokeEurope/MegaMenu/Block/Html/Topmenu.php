<?php

namespace CokeEurope\MegaMenu\Block\Html;

use CokeEurope\MegaMenu\Setup\Patch\Data\InstallNavCmsBlockCategoryAttribute;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu implements IdentityInterface
{
    /**
     * @var array
     */
    protected $identities = [];
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var array
     */
    private $categories = [];
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var array
     */
    private $navigationCmsBlocks = [];

    /**
     * Topmenu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param LoggerInterface $logger
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        LoggerInterface $logger,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->logger = $logger;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $categoryId = $this->getCategoryIdFromNode($child);
        $category = $this->getCategoryById($categoryId);

        $colStops = [];
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }
        $html .= '<div class="mega-menu-container submenu" style="display: none">';
        $html .= '<div class="mega-menu-wrapper">';

        $html .= '<ul class="mega-menu-child level' . $childLevel . ' ' . $childrenWrapClass . '">';
        $html .= '<li class="title"><span>'. __("Shop by Category"). '</span></li>';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';
        if (($navCmsBlock = $this->getNavigationCmsBlockHtml($category)) && $childLevel == 0) {
            $html .= '<div class="nav-cms-container">' . $this->getNavigationCmsBlockHtml($category) . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param Node $node
     * @return int|null
     */
    private function getCategoryIdFromNode(Node $node): ?int
    {
        if (!$node->getData('is_category')) {
            return null;
        }

        return (int)str_replace('category-node-', '', $node->getId());
    }

    /**
     * @param int|null $categoryId
     * @return CategoryInterface|void
     * @throws NoSuchEntityException
     */
    private function getCategoryById(?int $categoryId)
    {
        if (!$categoryId) {
            return;
        }

        if (!isset($this->categories[$categoryId])) {
            $this->categories[$categoryId] = $this->categoryRepository->get($categoryId);
        }
        return $this->categories[$categoryId];
    }

    /**
     * @param CategoryInterface|null $category
     * @return string
     * @throws LocalizedException
     */
    private function getNavigationCmsBlockHtml(?CategoryInterface $category): string
    {
        if (!$category || !$category->getData(InstallNavCmsBlockCategoryAttribute::NAVIGATION_CMS_BLOCK)) {
            return '';
        }

        if (!isset($this->navigationCmsBlocks[$category->getId()])) {
            $html = $this->getLayout()->createBlock(
                \Magento\Cms\Block\Block::class
            )->setBlockId(
                $category->getData(InstallNavCmsBlockCategoryAttribute::NAVIGATION_CMS_BLOCK)
            )->toHtml();

            $this->navigationCmsBlocks[$category->getId()] = $html;
        }

        return $this->navigationCmsBlocks[$category->getId()];
    }
}
