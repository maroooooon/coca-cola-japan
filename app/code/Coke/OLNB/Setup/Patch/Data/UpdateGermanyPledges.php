<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateGermanyPledges
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateGermanyPledges implements DataPatchInterface
{
    /**
     * Array of current values and new values and whitelist type
     */
    public const VALUE_TYPE_MAPPING = [
        'Stress' =>
            ['value' => 'hat Stress', 'type' => 'Dieses Jahr ____ keine Chance'],
        'mehr anstrengen' =>
            ['value' => 'mich mehr anstrengen', 'type' => 'Für dich werde ich ___ . Versprochen'],
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
     * UpdateGermanyPledges constructor.
     *
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param LoggerInterface $logger
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        WhitelistRepositoryInterface $whitelistRepository,
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->whitelistRepository = $whitelistRepository;
        $this->logger = $logger;
        $this->storeRepository = $storeRepository;
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
            $store = $this->storeRepository->get('germany_german');
            $this->updateValues((int) $store->getId());
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Update values for store
     *
     * @param int $storeId
     */
    private function updateValues(int $storeId)
    {
        foreach (self::VALUE_TYPE_MAPPING as $value => $data) {
            try {
                $whitelist = $this->whitelistRepository->getByValue($value, $storeId);
                $whitelist->setValue($data['value']);
                $this->whitelistRepository->save($whitelist);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
