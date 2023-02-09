<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateNorwayPledgeProductName
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateNorwayPledgeProductName  implements DataPatchInterface
{
    /** @var string product name value */
    public const PRODUCT_NAME_CURRENT = 'La oss____sammen';

    /** @var string New value for product name */
    public const PRODUCT_NAME_NEW = 'La os jobbe med __ sammen';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * UpdateNorwayPledgeProductName constructor.
     *
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param State $appState
     * @param Emulation $emulation
     */
    public function __construct(
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        State $appState,
        Emulation $emulation
    ) {
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->appState = $appState;
        $this->emulation = $emulation;
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Apply patch
     *
     * @return $this|PatchInterface
     * @throws Exception
     */
    public function apply(): PatchInterface
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'updateProductNames']);

        return $this;
    }

    /**
     * Update product names
     *
     * @return void
     */
    public function updateProductNames(): void
    {
        $this->emulation->startEnvironmentEmulation(Store::DEFAULT_STORE_ID, 'adminhtml');
        $this->searchCriteriaBuilder->addFilter('name', '%' . self::PRODUCT_NAME_CURRENT . '%', 'like');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $products = $this->productRepository->getList($searchCriteria);
        foreach ($products->getItems() as $product) {
            $name = $product->getName();
            $name = str_replace(self::PRODUCT_NAME_CURRENT, self::PRODUCT_NAME_NEW, $name);
            $product->setName($name);
            $product->setStoreId(Store::DEFAULT_STORE_ID);
            try {
                $this->productRepository->save($product);
            }  catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $this->emulation->stopEnvironmentEmulation();
    }
}
