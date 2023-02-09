<?php

namespace Coke\Cms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEgyptEnglishStore()
    {
        return $this->storeManager->getStore('egypt_en');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getEgyptArabicStore()
    {
        return $this->storeManager->getStore('egypt');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getTopoChicoEnglishStore()
    {
        return $this->storeManager->getStore('topo_chico_gr_en');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getTopoChicoGreeceStore()
    {
        return $this->storeManager->getStore('topo_chico_gr_gr');
    }

    /**
     * @return \Magento\Store\Api\Data\WebsiteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEgyptWebsite()
    {
        return $this->storeManager->getWebsite('egypt_website');
    }

    /**
     * @return \Magento\Store\Api\Data\WebsiteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOLNBTurkeyWebsite()
    {
        return $this->storeManager->getWebsite('olnb_turkey');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getOLNBTurkeyTurkishStore()
    {
        return $this->storeManager->getStore('turkey_turkish');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getONLBFranceStore()
    {
        return $this->storeManager->getStore('france_d2c');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getOLNBBelgiumLuxembourgFrStore()
    {
        return $this->storeManager->getStore('belgium_luxembourg_french');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getOLNBBelgiumLuxembourgNlStore()
    {
        return $this->storeManager->getStore('belgium_luxembourg_dutch');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getOLNBGreatBritainEnglishStore()
    {
        return $this->storeManager->getStore('great_britain_english');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getOLNBGermanyGermanStore()
    {
        return $this->storeManager->getStore('germany_german');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getOLNBNetherlandsDutchStore()
    {
        return $this->storeManager->getStore('netherlands_dutch');
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getJapanMarcheJapaneseStore()
    {
        return $this->storeManager->getStore('jp_marche_ja');
    }
}
