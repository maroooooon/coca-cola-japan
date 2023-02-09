<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Coke\OLNB\ViewModel\FixedProductTax;
use Exception;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateFinlandAttributeLabel
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateFinlandAttributeLabel implements DataPatchInterface
{
    /** @var string label for attribute bottle_deposit_fpt */
    public const FPT_BOTTLE_DEPOSIT_LABEL_FI = 'Pantti';

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpdateFinlandAttributeLabel constructor.
     *
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductAttributeRepositoryInterface $attributeRepository,
        StoreRepositoryInterface $storeRepository,
        LoggerInterface $logger
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->storeRepository = $storeRepository;
        $this->logger = $logger;
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
     */
    public function apply(): PatchInterface
    {
        try {
            $store = $this->storeRepository->get('finland_finnish');
            $storeId = $store->getId();
            $attribute = $this->attributeRepository->get(FixedProductTax::FPT_BOTTLE_DEPOSIT);
            $labels = $attribute->getFrontendLabels();
            foreach ($labels as $label) {
                if ($label->getStoreId() == $storeId) {
                    $label->setLabel(self::FPT_BOTTLE_DEPOSIT_LABEL_FI);
                    break;
                }
            }
            $this->attributeRepository->save($attribute);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
