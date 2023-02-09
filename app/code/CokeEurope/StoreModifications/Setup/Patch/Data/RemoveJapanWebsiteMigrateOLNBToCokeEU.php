<?php

namespace CokeEurope\StoreModifications\Setup\Patch\Data;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\InventorySales\Model\ResourceModel\DeleteSalesChannelToStockLink;
use Magento\InventorySales\Model\ResourceModel\UpdateSalesChannelWebsiteCode;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResource;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use Psr\Log\LoggerInterface;

class RemoveJapanWebsiteMigrateOLNBToCokeEU implements DataPatchInterface
{
	public const CATEGORY_NAME = 'Explore Collections';

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;
    /**
     * @var ConfigResource
     */
    private $configResource;
    /**
     * @var SalesChannel
     */
    private $deleteSalesChannelToStockLink;
    /**
     * @var StoreFactory
     */
    private $storeFactory;
    /**
     * @var StoreResource
     */
    private $storeResource;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var WebsiteResource
     */
    private $websiteResource;
    /**
     * @var GroupFactory
     */
    private $groupFactory;
    /**
     * @var GroupResource
     */
    private $groupResource;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;
    /**
     * @var UpdateSalesChannelWebsiteCode
     */
    private $updateSalesChannelWebsiteCode;

    /**
     * @var CategoryFactory
     */
	private CategoryFactory $categoryFactory;

    /** @var State  */
    private State $state;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface      $moduleDataSetup,
        ConfigResource                $configResource,
        DeleteSalesChannelToStockLink $deleteSalesChannelToStockLink,
        StoreFactory                  $storeFactory,
        StoreResource                 $storeResource,
        StoreRepositoryInterface      $storeRepository,
        LoggerInterface               $logger,
        WebsiteFactory                $websiteFactory,
        WebsiteResource               $websiteResource,
        WebsiteRepositoryInterface    $websiteRepository,
        GroupFactory                  $groupFactory,
        GroupResource                 $groupResource,
        GroupRepositoryInterface      $groupRepository,
        CollectionFactory             $configCollectionFactory,
        UpdateSalesChannelWebsiteCode $updateSalesChannelWebsiteCode,
        \Magento\Framework\Registry   $registry,
	    CategoryFactory $categoryFactory,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configResource = $configResource;
        $this->deleteSalesChannelToStockLink = $deleteSalesChannelToStockLink;
        $this->storeFactory = $storeFactory;
        $this->storeResource = $storeResource;
        $this->storeRepository = $storeRepository;
        $this->logger = $logger;
        $this->websiteFactory = $websiteFactory;
        $this->websiteResource = $websiteResource;
        $this->groupFactory = $groupFactory;
        $this->groupResource = $groupResource;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->updateSalesChannelWebsiteCode = $updateSalesChannelWebsiteCode;
        $registry->register('isSecureArea', true);
		$this->categoryFactory = $categoryFactory;
        $this->state = $state;
    }

    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        try {
            $this->state->setAreaCode(Area::AREA_CRONTAB);
        } catch (\Exception $e) {}

        $this->moduleDataSetup->getConnection()->startSetup();

        // Delete Coke Japan Magokoro, if it still exists (not to be confused with Coke Japan Marche / My Coke Store - Japan)
        $this->deleteJapanMagokoro();

        // Rename olnb_eu to coke_eu and remove olnb_ from group codes
        $this->renameOLNBEUToCokeEU();

        // Rename olnb_gb to coke_uk and move olnb_northern_ireland into coke_uk, and remove olnb_ from code
        $this->renameOLNBGBToCokeUK();

        // Make France Group and Store
        $this->createFranceStore();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    protected function deleteJapanMagokoro()
    {
        try {
            if (!$this->websiteRepository->get('japan_website') && !$this->storeRepository->get('japan') && !$this->storeRepository->get('japan_en') && !$this->getGroupByCode('japan_store')) {
                $this->logger->notice('Japan Magokoro was not found, bypassing.');
            }
            if ($this->storeRepository->get('japan')) {
                $this->logger->notice('The Japan Magokoro - Japanese store view (japan) exiwas found and is being deleted now.');
                $japanStore = $this->storeFactory->create();
                $this->storeResource->load($japanStore, 'japan', 'code');
                $this->storeResource->delete($japanStore);
            }
            if ($this->storeRepository->get('japan_en')) {
                $this->logger->notice('The Japan Magokoro - English store view (japan_en) was found and is being deleted now.');
                $japanStore = $this->storeFactory->create();
                $this->storeResource->load($japanStore, 'japan_en', 'code');
                $this->storeResource->delete($japanStore);
            }
            if ($this->getGroupByCode('japan_store')) {
                $this->logger->notice('The Japan Magokoro Store Group (japan_store) was found and is being deleted now.');
                $japanGroup = $this->groupFactory->create();
                $this->groupResource->load($japanGroup, 'japan_store', 'code');
                $this->groupResource->delete($japanGroup);
            }
            if ($this->websiteRepository->get('japan_website')) {
                $this->logger->notice('The Japan Magokoro Website (japan_website) was found and is being deleted now.');
                $japanWebsite = $this->websiteFactory->create();
                $this->websiteResource->load($japanWebsite, 'japan_website', 'code');
                $this->deleteConfig('websites', $japanWebsite->getId());
                $this->deleteSalesChannelToStockLink->execute('website',$japanWebsite->getCode());
                $this->websiteResource->delete($japanWebsite);
            }
        } catch (\Exception $e) {
            // Completely useless step.
        }
    }

    protected function renameOLNBEUToCokeEU()
    {
        try {
            if ($this->websiteRepository->get('olnb_eu')) {
                /** @var Website $cokeEU */
                $cokeEU = $this->websiteFactory->create();
                $this->websiteResource->load($cokeEU, 'olnb_eu', 'code');
                $cokeEU->setCode('coke_eu');
                $cokeEU->setName('Coke Europe');
                $this->websiteResource->save($cokeEU);
                $this->updateSalesChannelWebsiteCode->execute('olnb_eu', 'coke_eu');
            }
        } catch (\Exception $e) {
            // Site was probably renamed already
        }

	    $cokeEU = $this->websiteFactory->create();
	    $this->websiteResource->load($cokeEU, 'coke_eu', 'code');

        // Modify Belgium Group
        $this->recodeGroup('olnb_belgium_luxembourgh', 'belgium');
	    $this->renameStore('belgium_luxembourg_french', 'belgium_french');
	    $this->renameStore('belgium_luxembourg_dutch', 'belgium_dutch');

	    // Modify Ireland Group
        $this->recodeGroup('olnb_ireland', 'ireland');

        // Modify Germany Group
        $this->recodeGroup('olnb_germany', 'germany');

        // Modify Netherlands Group
        $this->recodeGroup('olnb_netherlands', 'netherlands');

        // Modify Finland Group
        $this->recodeGroup('olnb_finland', 'finland');

	    $this->fetchOrCreateEuropeProductCategory($cokeEU);
    }

	/**
	 * It creates a category called "Coca-Cola Products" if it doesn't already exist
	 * @param Website $cokeEu The website object
	 */
	public function fetchOrCreateEuropeProductCategory(Website $cokeEu)
	{
		$parentId = $cokeEu->getDefaultStore()->getRootCategoryId();
		$parentCategory = $this->categoryFactory->create()->load($parentId);

		$category = $this->categoryFactory->create();
		$cate = $category->getCollection()
			->addAttributeToFilter('name', self::CATEGORY_NAME)
			->getFirstItem();

		if (!$cate->getId()) {
			$category->setPath($parentCategory->getPath())
				->setParentId($parentId)
				->setName(self::CATEGORY_NAME)
				->setIsActive(true);
			$category->save();
		}
	}

    protected function renameOLNBGBToCokeUK()
    {
        try {
            if ($this->websiteRepository->get('olnb_gb')) {
                $cokeUK = $this->websiteFactory->create();
                $this->websiteResource->load($cokeUK, 'olnb_gb', 'code');
                $cokeUK->setCode('coke_uk');
                $cokeUK->setName('Coke United Kingdom');
                $this->websiteResource->save($cokeUK);
                $this->updateSalesChannelWebsiteCode->execute('olnb_gb', 'coke_gb');
            }
        } catch (\Exception $e) {
            // Site was probably renamed already from config.php change
        }

        // Modify Northern Ireland Group
        $this->recodeGroup('olnb_northern_ireland', 'northern_ireland', $cokeUK->getId());

        // Modify Great Britain Group
        $this->recodeGroup('olnb_great_britain', 'great_britain', $cokeUK->getId());

        // Delete Northern Ireland website
        try {
            if ($this->websiteRepository->get('olnb_ni')) {
                $northernIrelandWebsite = $this->websiteFactory->create();
                $this->websiteResource->load($northernIrelandWebsite, 'olnb_ni', 'code');
                $this->deleteConfig('websites', $northernIrelandWebsite->getId());
                $this->websiteResource->delete($northernIrelandWebsite);
            }
        } catch (\Exception $e) {
            // Site was probably deleted already from config.php
        }

    }

    private function deleteConfig($scope, $scopeId)
    {
        $configCollection = $this->configCollectionFactory->create()
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('scope_id', $scopeId);
        foreach ($configCollection as $config) {
            $this->configResource->delete($config);
        }
    }

    private function recodeGroup($oldName, $newName, $newWebsiteParent = null)
    {
        if ($this->getGroupByCode($oldName)) {
            $group = $this->groupFactory->create();
            $this->groupResource->load($group, $oldName, 'code');
            if ($group->getId()) {
                $group->setCode($newName);
                if ($newWebsiteParent) {
                    $group->setWebsiteId($newWebsiteParent);
                }
                $this->groupResource->save($group);
            }
        }
    }

    private function getGroupByCode($code)
    {
        $group = $this->groupFactory->create();
        $this->groupResource->load($group, $code, 'code');

        return $group;
    }

	/**
	 * @param $oldStoreCode
	 * @param $newStoreCode
	 * @return void
	 * @throws \Magento\Framework\Exception\AlreadyExistsException
	 */
	private function renameStore($oldStoreCode, $newStoreCode): void
	{
		try {
			/** @var Store $store */
			$store = $this->storeFactory->create();
			$this->storeResource->load($store, $oldStoreCode, 'code');
			$store->setCode($newStoreCode);
			$this->storeResource->save($store);
		} catch (\Exception $e) {
//			Nothing to do
		}
	}

	/**
	 * @param Website $cokeEU
	 * @return void
	 * @throws NoSuchEntityException
	 * @throws \Magento\Framework\Exception\AlreadyExistsException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	private function createFranceStore(): void
	{
        $cokeEU = $this->websiteFactory->create();
        $this->websiteResource->load($cokeEU, 'coke_eu', 'code');

		/* Creating a new store group for the France website. */
		$euStore = $this->storeRepository->get('ireland_english');
		$euGroup = $this->groupRepository->get($euStore->getStoreGroupId()); // To get the store root category id

		/** @var Group $franceGroup */
		$franceGroup = $this->groupFactory->create();
		$franceGroup->setWebsiteId($cokeEU->getId());
		$franceGroup->setName('France');
		$franceGroup->setCode('france_french');
		$franceGroup->setRootCategoryId($euGroup->getRootCategoryId());
		$franceGroup->save();

		/** @var  \Magento\Store\Model\Store $franceStore */
		$franceStore = $this->storeFactory->create();
		$franceStore->load('france_french');
		if (!$franceStore->getId()) {
			$group = $this->groupFactory->create();
			$group->load($franceGroup->getId());
			$franceStore->setCode('france_french');
			$franceStore->setName('France');
			$franceStore->setWebsite($cokeEU);
			$franceStore->setGroupId($franceGroup->getId());
			$franceStore->setData('is_active', '1');
			$this->storeResource->save($franceStore);
		}
	}
}
