<?php

namespace Coke\OLNB\Block\Catalog;

use Illuminate\Support\Arr;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductList
 *
 * @package Coke\OLNB\Block\Catalog
 */
class ProductList implements ArgumentInterface
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var array
     */
    private $configurableChildren = [];
    /**
     * @var Repository
     */
    private $assetRepo;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ProductList constructor.
     *
     * @param CollectionFactory $productCollectionFactory
     * @param Repository $assetRepo
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Repository $assetRepo,
        StoreManagerInterface $storeManager,
        RequestInterface $request
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getConfigurableChildren(Product $product): array
    {
        if (!isset($this->configurableChildren[$product->getId()])) {
            $simpleIds = $product->getTypeInstance()
                ->getUsedProductIds($product);

            // Get first configurable's children
            $this->configurableChildren[$product->getId()] = $simples = $this->productCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in '=> $simpleIds])
                ->addAttributeToSelect('*')
                ->getItems();
        }

        return $this->configurableChildren[$product->getId()];
    }

    /**
     * @param Product $product configurable product
     * @return array
     */
    public function getMarketFlavors(Product $product)
    {
        if (empty($children = $this->getConfigurableChildren($product))) {
            return [];
        }

        $attribute = current($children)->getResource()->getAttribute('flavor');
        $brands = [];

        foreach ($children as $simple) {
            $brands[$simple->getData('flavor')] = $brands[$simple->getData('flavor')] ??
                $attribute->usesSource() ? $attribute->getSource()->getOptionText($simple->getData('flavor')) : null;
        }

        return array_filter($brands);
    }

    /**
     * Get flavour image url
     *
     * @param string $marketFlavor
     *
     * @return string
     */
    public function getMarketFlavorImageUrl(string $marketFlavor): string
    {
        $prefix = $this->getImagePrefixForSpecificStore();
        $marketFlavor = $marketFlavor . $prefix;
        $id = preg_replace('/\s+/', '-', strtolower($marketFlavor));
        $url = $this->assetRepo->getUrlWithParams('images/_dev/', [
                '_secure' => $this->request->isSecure()
            ]) . '/' . $id . '.jpg';

        return $url;
    }

    /**
     * Get current Store Code
     *
     * @return string
     */
    private function getStoreCode(): string
    {
        try {
            $store = $this->storeManager->getStore();
            $storeCode = $store->getCode();
        } catch (NoSuchEntityException $e) {
            $storeCode = '';
        }

        return $storeCode;
    }

    /**
     * Get image name prefix for a specific store
     *
     * @return string
     */
    private function getImagePrefixForSpecificStore(): string
    {
        $storeCode = $this->getStoreCode();
        switch ($storeCode) {
            case 'germany_german':
                $prefix = '-de';
                break;
            default:
                $prefix = '';
        }

        return $prefix;
    }
}
