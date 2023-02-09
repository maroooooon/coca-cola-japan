<?php
declare(strict_types=1);

namespace Coke\OLNB\Setup\Patch\Data;

use Exception;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateTurkeyContactUsCMSBlock
 *
 * @package Coke\OLNB\Setup\Patch\Data
 */
class UpdateTurkeyContactUsCMSBlock  implements DataPatchInterface
{
    /** @var string Turkish store code */
    public const TURKISH_STORE_CODE = 'turkey_turkish';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /** @var int Store ID */
    private $storeId;

    /**
     * UpdateTurkeyContactUsCMSBlock constructor.
     *
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param BlockRepositoryInterface $blockRepository
     * @param WriterInterface $configWriter
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        LoggerInterface $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        BlockRepositoryInterface $blockRepository,
        WriterInterface $configWriter,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->blockRepository = $blockRepository;
        $this->storeRepository = $storeRepository;
        $this->configWriter = $configWriter;
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
        try {
            $block = $this->getBlock();
            $this->updateContent($block);
            $this->setContactEmail();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Get block by an identifier and store
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getBlock(): BlockInterface
    {
        $store = $this->storeRepository->get(self::TURKISH_STORE_CODE);
        $this->storeId = $store->getId();
        $this->searchCriteriaBuilder->addFilter(Store::STORE_ID, $this->storeId);
        $this->searchCriteriaBuilder->addFilter(BlockInterface::IDENTIFIER, 'contact-us-info');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setPageSize(1);
        $searchCriteria->setCurrentPage(1);
        $blocks = $this->blockRepository->getList($searchCriteria);
        $blocks = $blocks->getItems();

        return array_pop($blocks);
    }

    /**
     * Update content, delete unnecessary content related to form style
     *
     * @param BlockInterface $block
     *
     * @return void
     */
    public function updateContent(BlockInterface $block): void
    {
        try {
            $content =
                '<div class="contact-info cms-content">
    <div class="block block-contact-info">
        <div class="block">
            <div class="block-content">
               <strong>COCA-COLA İLETİŞİM MERKEZİ’NE HOŞ GELDİN!</strong> <br />
               0 850 201 30 40 / 444 30 40<br />
               iletisimmerkezi@coca-cola.com
            </div>
         </div>

    </div>
</div>';
            $block->setContent($content);
            $this->blockRepository->save($block);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Set Contact Email for Turkish store
     *
     * @return void
     */
    private function setContactEmail(): void
    {
        $this->configWriter->save(
            ConfigInterface::XML_PATH_EMAIL_RECIPIENT,
            'iletisimmerkezi@coca-cola.com',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
