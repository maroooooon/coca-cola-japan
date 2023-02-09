<?php

namespace Bounteous\MaintenanceMode\Model\Config\Source;

use Exception;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class CmsPages implements OptionSourceInterface
{
    /**
     * @var FilterBuilder
     */
    protected FilterBuilder $filterBuilder;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $httpRequest;

    /**
     * @var PageRepositoryInterface
     */
    protected PageRepositoryInterface $pageRepositoryInterface;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * CmsPages constructor.
     *
     * @param FilterBuilder           $filterBuilder
     * @param Http                    $httpRequest
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param SearchCriteriaBuilder   $searchCriteriaBuilder
     */
    public function __construct(
        FilterBuilder           $filterBuilder,
        Http                    $httpRequest,
        PageRepositoryInterface $pageRepositoryInterface,
        SearchCriteriaBuilder   $searchCriteriaBuilder
    ) {
        $this->filterBuilder           = $filterBuilder;
        $this->httpRequest             = $httpRequest;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder   = $searchCriteriaBuilder;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $optionArray = [];

        $pages = $this->getCmsPageCollection();
        if ($pages instanceof LocalizedException) {
            throw $pages;
        }
        $cnt = 0;
        foreach ($pages as $page) {
            $optionArray[$cnt]['value']   = $page->getIdentifier();
            $optionArray[$cnt++]['label'] = $page->getTitle();
        }
        return $optionArray;
    }

    /**
     * @return Exception|PageInterface[]|LocalizedException
     */
    public function getCmsPageCollection()
    {
        $filter = $this->filterBuilder
            ->setField("is_active")
            ->setValue(1)
            ->setConditionType("eq")
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder->addFilters([$filter])->create();

        $store      = $this->httpRequest->getParam('store', 0);
        $collection = $pages = [];
        try {
            $collection = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return $e;
        }

        if (isset($collection) && !empty($collection)) {
            foreach ($collection as $page) {
                $pageStores = $page->getStores();

                if (in_array($store, $pageStores) || in_array(0, $pageStores)) {
                    $pages[] = $page;
                }
            }
        }
        return $pages;
    }
}
