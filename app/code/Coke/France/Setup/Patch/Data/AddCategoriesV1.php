<?php

namespace Coke\France\Setup\Patch\Data;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\TreeFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class AddCategoriesV1 implements DataPatchInterface
{
    /**
     * @var array
     */
    private $categoryTree;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var TreeFactory
     */
    private $resourceCategoryTreeFactory;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * AddBrands constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param TreeFactory $resourceCategoryTreeFactory
     * @param CategoryFactory $categoryFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        TreeFactory $resourceCategoryTreeFactory,
        CategoryFactory $categoryFactory,
        StoreRepositoryInterface $storeRepository,
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceCategoryTreeFactory = $resourceCategoryTreeFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeRepository = $storeRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            \Coke\France\Setup\Patch\Data\AddPrefilledPhraseLine1ProductAttributeV1::class,
            \Coke\France\Setup\Patch\Data\AddPrefilledPhraseLine2ProductAttributeV1::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|DataPatchInterface
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $categories = [
            "Personnalisation" => [
                "Personnalisez Un Produit" => [
                    "Bouteilles en verre",
                    "Canettes",
                    "Grande quantité: de 50 à 200 bouteilles"
                ],
                "Célébrer une occasion" => [
                    "Nouvel An 2021",
                    "Anniversaire",
                    "Fêtes",
                    "Je t'aime",
                    "Mariage",
                    "Evènements professionnels",
                    "Evènements sportifs"
                ]
            ],
            "Editions limitées",
            "Arrondissements",
            "Kit Noël",
            "Pack Paris Saint-Germain",
            "Kit du Supporter"
        ];

        foreach ($this->getCategoryPaths($categories) as $fullPath) {
            $path = explode("/", $fullPath);
            $currentNames = [];

            foreach ($path as $i => $name) {
                $currentPath = implode("/", $currentNames);
                $newPath = $currentPath . '/' . $name;
                $category = $this->getCategoryByPath($newPath);
                $currentNames[] = $name;

                if (!$category) {
                    $parentCategory = $this->getCategoryByPath($currentPath);
                    $category = $this->categoryFactory->create();
                    $data = [
                        'parent_id' => $parentCategory->getId(),
                        'name' => $name,
                        'is_active' => true,
                        'is_anchor' => true,
                        'include_in_menu' => true,
                        'url_key' => $category->formatUrlKey($name),
                    ];
                    $category->setData($data)
                        ->setPath($parentCategory->getData('path'))
                        ->setAttributeSetId($category->getDefaultAttributeSetId());
                    $category->save();
                }
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @param array $categories
     * @param array $parents
     * @return array
     */
    public function getCategoryPaths(array $categories, array $parents = []): array
    {
        $paths = [];

        foreach ($categories as $root => $children) {
            if (is_array($children)) {
                // @phpcs:ignore
                $paths = array_merge($paths, $this->getCategoryPaths($children, array_merge($parents, [$root])));
            } else {
                // @phpcs:ignore
                $paths[] = implode("/", array_merge($parents, [$children]));
            }
        }

        return $paths;
    }

    /**
     * Get category name by path
     *
     * @param string $path
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getCategoryByPath($path)
    {
        $names = array_filter(explode('/', $path));
        $tree = $this->getTree();
        foreach ($names as $name) {
            $tree = $this->findTreeChild($tree, $name);
            if (!$tree) {
                $tree = $this->findTreeChild($this->getTree(null, true), $name);
            }
            if (!$tree) {
                break;
            }
        }
        return $tree;
    }

    /**
     * Get child categories
     *
     * @param \Magento\Framework\Data\Tree\Node $tree
     * @param string $name
     * @return mixed
     */
    protected function findTreeChild($tree, $name)
    {
        $foundChild = null;
        if ($name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $foundChild = $child;
                    break;
                }
            }
        }
        return $foundChild;
    }

    /**
     * Get category tree
     *
     * @param int|null $rootNode
     * @param bool $reload
     * @return \Magento\Framework\Data\Tree\Node
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    protected function getTree($rootNode = null, $reload = false)
    {
        if (!$this->categoryTree || $reload) {
            if ($rootNode === null) {
                $rootNode = $this->getFranceDefaultCategory();

                if (!$rootNode) {
                    throw new \Exception(__('Unable to install Coke France categories.'));
                }
            }
            $tree = $this->resourceCategoryTreeFactory->create();
            $node = $tree->loadNode($rootNode)->loadChildren();

            $tree->addCollectionData(null, false, $rootNode);

            $this->categoryTree = $node;
        }
        return $this->categoryTree;
    }

    /**
     * @return int|null
     * @throws \Exception
     */
    private function getFranceDefaultCategory(): ?int
    {
        try {
            return $this->categoryCollectionFactory->create()
                ->addAttributeToFilter('name', 'France D2C Root')
                ->setPageSize(1)
                ->getFirstItem()
                ->getId();
        } catch (LocalizedException $e) {
            throw new \Exception(__('Unable to install Coke France categories.'));
        }
    }
}
