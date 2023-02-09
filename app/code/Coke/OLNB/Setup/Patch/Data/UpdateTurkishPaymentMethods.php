<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Payment\Model\Method\Free;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateTurkishPaymentMethods
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateTurkishPaymentMethods implements DataPatchInterface
{
    /** @var string OLNB Turkey website code */
    public const OLNB_TURKEY_WEBSITE_CODE = 'olnb_turkey';

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var int
     */
    private $websiteId;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpdateTurkishPaymentMethods constructor.
     *
     * @param WriterInterface $configWriter
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        WriterInterface $configWriter,
        WebsiteRepositoryInterface $websiteRepository,
        LoggerInterface $logger
)
    {
        $this->configWriter = $configWriter;
        $this->websiteRepository = $websiteRepository;
        $this->logger = $logger;
    }

    /**
     * Get array of patches that have to be executed prior to this
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
     * @return $this|DataPatchInterface
     */
    public function apply(): DataPatchInterface
    {
        $methods = [
            Free::XML_PATH_PAYMENT_FREE_ACTIVE,
            'carriers/freeshipping/active'
        ];
        try {
            $turkeyWebsite = $this->websiteRepository->get(self::OLNB_TURKEY_WEBSITE_CODE);
            $this->websiteId = $turkeyWebsite->getId();
            foreach ($methods as $method) {
                $this->disableMethod($method);
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Save config to DB
     *
     * @param string $method
     */
    private function disableMethod(string $method): void
    {
        $this->configWriter->save(
            $method,
            0,
            ScopeInterface::SCOPE_WEBSITES,
            $this->websiteId
        );
    }
}
