<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Coke\Whitelist\Api\Data\WhitelistInterface;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;
use Coke\Whitelist\Api\Data\WhitelistInterfaceFactory;

/**
 * Class UpdateBelgiumResolutions
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateBelgiumResolutions implements DataPatchInterface
{
    /** @var string[] Whitelist values array */
    public const WHITELIST_VALUES = [
        'rien',
        'ton sourire',
        'ton regard',
        'ta présence',
        'ton rire',
        'nos sorties',
        'nos câlins',
        'vos visites',
        'nos plans de dernière minute',
        'ma famille',
        'mes amies',
        'mon chien',
        'mes parents',
        'ma petite amie',
        'mon amie',
        'ton amour',
        'nos conversations',
        'tes bons conseils',
        'vos appels',
        'vos compliments',
        'tes bons petis plats',
    ];

    /**
     * @var WhitelistRepositoryInterface
     */
    private $whitelistRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var WhitelistInterfaceFactory
     */
    private $whitelistFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * UpdateBelgiumResolutions constructor.
     *
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param LoggerInterface $logger
     * @param Emulation $emulation
     * @param State $appState
     * @param WhitelistInterfaceFactory $whitelistFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     * @param ProductRepositoryInterface $productRepository
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        WhitelistRepositoryInterface $whitelistRepository,
        LoggerInterface $logger,
        Emulation $emulation,
        State $appState,
        WhitelistInterfaceFactory $whitelistFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WhitelistTypeRepositoryInterface $whitelistTypeRepository,
        ProductRepositoryInterface $productRepository,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->whitelistRepository = $whitelistRepository;

        $this->logger = $logger;
        $this->storeRepository = $storeRepository;
        $this->emulation = $emulation;
        $this->whitelistFactory = $whitelistFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
        $this->productRepository = $productRepository;
        $this->appState = $appState;
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
            $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'disableResolution']);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        $this->updateWhiteList();

        return $this;
    }

    /**
     * Disable unused resolutions
     *
     * @return void
     */
    public function disableResolution(): void
    {
        try {
            $product = $this->productRepository->get('BE-FR-STD330-07');
            $product->setStatus(0);
            $this->productRepository->save($product);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }


    /**
     * Get store by code
     *
     * @return int
     * @throws NoSuchEntityException
     */
    private function getStore(): int
    {
        $store = $this->storeRepository->get('belgium_luxembourg_french');

        return (int) $store->getId();
    }

    /**
     * Update Whitelist values, add missing values, disable unused
     *
     * @return void
     */
    private function updateWhiteList(): void
    {
        try {
            $storeId = (int)$this->getStore();
            $whitelistType = $this->whitelistTypeRepository->getByName('Je ne prendrai plus jamais ____________ pour acquis');
            $typeId = (int) $whitelistType->getTypeId();
            $this->disableWhitelist($typeId);
            foreach (self::WHITELIST_VALUES as $whitelistValue) {
                try {
                    $this->whitelistRepository->getByValue($whitelistValue, $storeId);
                } catch (NoSuchEntityException $e) {
                    $this->createWhitelist($whitelistValue, $typeId, $storeId);
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Disable unused values
     *
     * @param int $typeId
     *
     * @return void
     */
    private function disableWhitelist(int $typeId): void
    {
        $this->searchCriteriaBuilder->addFilter('type_id', $typeId);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $lists = $this->whitelistRepository->getList($searchCriteria);
        foreach ($lists->getItems() as $list) {
            if (!in_array($list->getValue(), self::WHITELIST_VALUES)) {
                $list->setIsApproved(false);
                try {
                    $this->whitelistRepository->save($list);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }

    /**
     * Create whitelist
     *
     * @param string $whitelistValue
     * @param int $typeId
     * @param int $storeId
     *
     * @return void
     */
    private function createWhitelist(string $whitelistValue, int $typeId, int $storeId): void
    {
        /** @var WhitelistInterface $whitelist */
        $whitelist = $this->whitelistFactory->create();
        $whitelist->setIsApproved(true);
        $whitelist->setValue($whitelistValue);
        $whitelist->setTypeId($typeId);
        $whitelist->setStoreId($storeId);
        try {
            $this->whitelistRepository->save($whitelist);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
