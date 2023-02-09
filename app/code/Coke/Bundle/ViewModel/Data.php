<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Coke\Bundle\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Data implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Data constructor.
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $id
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id)
    {
        $product = $this->productRepository->getById($id);
        return $product;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getContainerSize($id) {
        return $this->getProductById($id)->getAttributeText('container');
    }

    /**
     * @param $id
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBottleSizeGroup($id){
        if($this->getContainerSize($id)){
            $sizeStr = strtolower($this->getContainerSize($id));
            if (strpos($sizeStr, 'ml') !== false) {
                $size = trim(str_replace('ml', '', $sizeStr));
                if((int)$size > 600){
                    return 2;
                } else {
                    return 1;
                }
            } else if (strpos($sizeStr, 'liter') !== false) {
                return 2;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
}
