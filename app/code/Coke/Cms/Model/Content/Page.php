<?php

namespace Coke\Cms\Model\Content;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Cms\Model\PageFactory;

class Page extends AbstractContent
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Page constructor.
     * @param PageRepositoryInterface $pageRepository
     * @param PageFactory $blockFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        PageRepositoryInterface $pageRepository,
        PageFactory $blockFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $blockFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $identifier
     * @param string $title
     * @return PageInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEntity(string $identifier, string $title ='')
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $identifier)->addFilter('title', $title)
            ->create();

        $result = $this->pageRepository->getList($searchCriteria);

        if ($result->getTotalCount() === 0) {
            return $this->createEntity($identifier);
        }

        $items = $result->getItems();
        return $items[array_keys($items)[0]];
    }

    /**
     * @param string $identifier
     * @return PageInterface
     */
    protected function createEntity(string $identifier)
    {
        return $this->pageFactory->create()
            ->setIdentifier($identifier);
    }

    /**
     * @param string $identifier
     * @param string $content
     * @param array $changes
     * @return PageInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyChanges(string $identifier, string $content, array $changes)
    {
        $entity = $this->getEntity($identifier, $changes['title']);
        $entity->setContent($content);
        $entity->addData($changes);
        $this->pageRepository->save($entity);
        return $entity;
    }

    /**
     * @param string $identifier
     * @param string $title
     * @return PageInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEntityByStoreId(string $identifier, string $storeId ='')
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $identifier)
            ->addFilter('store_id', $storeId)
            ->create();

        $result = $this->pageRepository->getList($searchCriteria);

        if ($result->getTotalCount() === 0) {
            return $this->createEntity($identifier);
        }

        $items = $result->getItems();
        return $items[array_keys($items)[0]];
    }

    /**
     * @param string $identifier
     * @param string $content
     * @param array $changes
     * @return PageInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyChangesByStoreId(string $identifier, string $content, array $changes)
    {
        $entity = $this->getEntityByStoreId($identifier, $changes['store_id'][0]);
        $entity->setContent($content);
        $entity->addData($changes);
        $this->pageRepository->save($entity);
        return $entity;
    }
}
