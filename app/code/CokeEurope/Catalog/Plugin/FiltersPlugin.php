<?php
/**
 * FiltersPlugin
 *
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author    tanya.lamontagne@bounteous.com
 */

namespace CokeEurope\Catalog\Plugin;

use CokeEurope\Catalog\ViewModel\FiltersViewModel;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class FiltersPlugin
{
    private StoreManagerInterface $storeManager;
    private WebsiteRepositoryInterface $websiteRepository;

    /**
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        WebsiteRepositoryInterface $websiteRepository,
        StoreManagerInterface      $storeManager
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * If the current website is the Europe website, then remove all filters except for the ones in the
     * `FiltersViewModel::FILTER_ATTRIBUTE_LABELS` array. Otherwise, remove all filters except for the ones in the
     * `self::COKE_EUROPE_FILTERS` array
     *
     * @param FilterList $subject The object that the method is being called on.
     * @param array $result The array of filters that will be returned by the method.
     * @return array The result of the method is an array of filters.
     */
    public function afterGetFilters(FilterList $subject, array $result): array
    {
        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();

        try {
            $europeWebsiteId = $this->getEuropeWebsiteId();
            $ukWebsiteId = $this->getUkWebsiteId();
        } catch (NoSuchEntityException $e) {
            $europeWebsiteId = $ukWebsiteId = 0; //We'll silently ignore this
        }

        if ($currentWebsiteId === $europeWebsiteId || $currentWebsiteId === $ukWebsiteId) {
            foreach ($result as $key => $filter) {
                if (!isset(FiltersViewModel::FILTER_ATTRIBUTE_LABELS[$filter->getRequestVar()])) {
                    unset($result[$key]);
                }
            }
        } else {
            foreach ($result as $key => $filter) {
                if (isset(FiltersViewModel::FILTER_ATTRIBUTE_LABELS[$filter->getRequestVar()]) && $filter->getRequestVar() !== 'cat') {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * Get the website with the code 'coke_eu' id from the website repository.
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getEuropeWebsiteId(): WebsiteInterface
    {
        return $this->websiteRepository->get('coke_eu')->getId();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getUkWebsiteId(): WebsiteInterface
    {
        return $this->websiteRepository->get('coke_uk')->getId();
    }
}
