<?php

namespace CokeEurope\StoreModifications\Model\Content;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Cms\Model\BlockFactory;

class Block extends AbstractContent
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Block constructor.
     * @param BlockRepositoryInterface $blockRepository
     * @param BlockFactory $blockFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        BlockFactory $blockFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $identifier
     * @param string $title
     * @return BlockInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEntity(string $identifier, string $title ='')
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $identifier)->addFilter('title', $title)
            ->create();

        $result = $this->blockRepository->getList($searchCriteria);

        if ($result->getTotalCount() === 0) {
            return $this->createEntity($identifier);
        }

        $items = $result->getItems();
        return $items[array_keys($items)[0]];
    }

    /**
     * @param string $identifier
     * @return BlockInterface
     */
    protected function createEntity(string $identifier)
    {
        return $this->blockFactory->create()
            ->setIdentifier($identifier);
    }

    /**
     * @param string $identifier
     * @param string $content
     * @param array $changes
     * @return BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyChanges(string $identifier, string $content, array $changes)
    {
        $entity = $this->getEntity($identifier, $changes['title']);
        $entity->setContent($content);
        $entity->addData($changes);
        $this->blockRepository->save($entity);
        return $entity;
    }

    /**
     * @param string $identifier
     * @param string $storeId
     * @return BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEntityByStoreId(string $identifier, string $storeId ='')
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $identifier)->addFilter('store_id', $storeId)
            ->create();

        $result = $this->blockRepository->getList($searchCriteria);

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
     * @return BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyChangesByStoreId(string $identifier, string $content, array $changes)
    {
        $entity = $this->getEntityByStoreId($identifier, $changes['store_id'][0]);
        $entity->setContent($content);
        $entity->addData($changes);
        $this->blockRepository->save($entity);
        return $entity;
    }

}
