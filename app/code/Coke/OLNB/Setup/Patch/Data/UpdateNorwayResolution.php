<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateNorwayResolution
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateNorwayResolution implements DataPatchInterface
{
    /** @var string Current resolution name */
    public const CURRENT_RESOLUTION = 'Jeg vil aldri _____ uten deg igjen';

    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpdateGermanyPledges constructor.
     *
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        WhitelistTypeRepositoryInterface $whitelistTypeRepository,
        LoggerInterface $logger
    ) {
        $this->whitelistTypeRepository = $whitelistTypeRepository;
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
            $this->updateValues();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Update value
     *
     * @throws NoSuchEntityException
     * @throws Exception
     */
    private function updateValues()
    {
        $whitelistType = $this->whitelistTypeRepository->getByName(self::CURRENT_RESOLUTION);
        $whitelistType->setName($this->replaceResolution($whitelistType->getName()));
        $whitelistType->setLabel($this->replaceResolution($whitelistType->getLabel()));
        $this->whitelistTypeRepository->save($whitelistType);
    }

    /**
     * Replace string with correct value
     *
     * @param string $string
     *
     * @return string
     */
    private function replaceResolution(string $string): string
    {
        $result = str_replace('Jeg vil', 'Jeg skal', $string);

        return $result;
    }
}
