<?php

namespace CokeEurope\StoreModifications\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        parent::__construct($context);

    }

    /**
     * @return WebsiteInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeWebsite(): WebsiteInterface
    {
        return $this->websiteRepository->get('coke_eu');
    }

    /**
     * @return WebsiteInterface
     * @throws NoSuchEntityException
     */
    public function getUkWebsite(): WebsiteInterface
    {
        return $this->websiteRepository->get('coke_uk');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeIrelandEnglishStore()
    {
        return $this->storeManager->getStore('ireland_english');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeNetherlandsDutchStore()
    {
        return $this->storeManager->getStore('netherlands_dutch');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeFinlandFinnishStore()
    {
        return $this->storeManager->getStore('finland_finnish');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeGermanyGermanStore()
    {
        return $this->storeManager->getStore('germany_german');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeFranceFrenchStore()
    {
        return $this->storeManager->getStore('france_french');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeBelgiumFrenchStore()
    {
        return $this->storeManager->getStore('belgium_french');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEuropeBelgiumDutchStore()
    {
        return $this->storeManager->getStore('belgium_dutch');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getUkIrelandEnglishStore()
    {
        return $this->storeManager->getStore('northern_ireland_english');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getUkGreatBritainEnglishStore()
    {
        return $this->storeManager->getStore('great_britain_english');
    }
}
