<?php

namespace Coke\Whitelist\ViewModel;

use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class WhiteListOptions implements  ArgumentInterface
{
    /**
     * @var CollectionFactory
     */
    private $whitelistCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * WhiteListOptions constructor.
     * @param CollectionFactory $whitelistCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
      CollectionFactory $whitelistCollectionFactory,
      StoreManagerInterface $storeManager,
      WhitelistTypeRepositoryInterface $whitelistTypeRepository,
      ModuleConfig $moduleConfig
    ) {
        $this->whitelistCollectionFactory = $whitelistCollectionFactory;
        $this->storeManager = $storeManager;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
        $this->moduleConfig = $moduleConfig;
    }

    public function getOptions($whiteListTypeId)
    {
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFieldToSelect(['entity_id','value'])
            ->addFilter('type_id', $whiteListTypeId)
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id',  $this->storeManager->getStore()->getId());

        return $collection->load()->toOptionArray();
    }

    public function getOptionValues($whiteListTypeId)
    {
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFieldToSelect(['value'])
            ->addFilter('type_id', $whiteListTypeId)
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id',  $this->storeManager->getStore()->getId());

        return $collection->load()->getColumnValues('value');
    }

    public function getTypeLabels($whitelistTypeId)
    {
        $whitelistType = $this->whitelistTypeRepository->getById($whitelistTypeId);
        $whitelistTypeLabel = $whitelistType->getLabel();

        return explode(PHP_EOL, $whitelistTypeLabel);
    }

    public function isNamesEnabled(): bool
    {
       return $this->moduleConfig->isNamesEnabled();
    }

    public function getIllegalCharacters(): string
    {
        return $this->moduleConfig->getIllegalCharacters();
    }
}
