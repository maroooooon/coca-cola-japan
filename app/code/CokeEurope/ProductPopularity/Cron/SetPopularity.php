<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\ProductPopularity\Cron;

use DateTime;
use Exception;
use CokeEurope\ProductPopularity\Helper\Config as PopularityHelper;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollection;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class SetPopularity
{
	private PopularityHelper $popularityHelper;
	private OrderItemCollection $orderItemsCollection;
	private ProductResourceModel $productResourceModel;
	private ProductRepositoryInterface $productRepository;
	private SearchCriteriaBuilder $searchCriteriaBuilder;

	/**
	 * @param PopularityHelper $popularityHelper
	 * @param OrderItemCollection $orderItemsCollection
	 * @param ProductResourceModel $productResourceModel
	 * @param ProductRepositoryInterface $productRepository
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 */
	public function __construct(
		PopularityHelper $popularityHelper,
		OrderItemCollection $orderItemsCollection,
		ProductResourceModel $productResourceModel,
		ProductRepositoryInterface $productRepository,
		SearchCriteriaBuilder $searchCriteriaBuilder
	)
    {
		$this->popularityHelper = $popularityHelper;
		$this->orderItemsCollection = $orderItemsCollection;
		$this->productResourceModel = $productResourceModel;
		$this->productRepository = $productRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
	}

	/**
	 * Execute the cron
	 * @return void
	 * @throws Exception
	 */
    public function execute()
    {
		// Skip if not enabled
		if(!$this->popularityHelper->isEnabled()) {
			return;
		}
		
		$yesterday = (new DateTime())->modify('-24 hours');
		$attributeMap = $this->popularityHelper->getPopularityAttributeMap();
		$messageOptions = $this->popularityHelper->getMessageOptions();
		$orderItems = $this->orderItemsCollection->create();
		$orderItems->addFieldToFilter('product_type', ['eq' => 'configurable']);
		$orderItems->addFieldToFilter('created_at', ['gteq' => $yesterday]);

		foreach($orderItems as $orderItem) {
			$options = $orderItem->getProductOptions();
			$attributes = $options['attributes_info'];
			$customOptions = $options['options'];
			$messageKey = array_search('Message', array_column($customOptions, 'label'));
			$messageValue = $customOptions[$messageKey]['value'];
			$messageValueId = array_key_exists($messageValue, $messageOptions) ? $messageOptions[$messageValue] : null;

			// Skip items with custom message
			if(!$messageValueId) {
				continue;
			}

			// Create an array of items with that have values for all the required attributes
			$selectedOptions = [];
			$selectedOptions['prefilled_message'] = $messageValueId;
			foreach($attributes as $attr) {
				if(!array_key_exists($attr['option_id'], $attributeMap)) {
					continue;
				} else {
					$selectedOptions[$attributeMap[$attr['option_id']]] = $attr['option_value'];
				}
			}

			// Add filters to search criteria
			foreach($selectedOptions as $code => $value) {
				$this->searchCriteriaBuilder->addFilter($code, $value);
			}

			// Search for items to update
			$searchCriteria = $this->searchCriteriaBuilder->create();
			$searchResult = $this->productRepository->getList($searchCriteria);

			// Update popularity for each item
			foreach($searchResult->getItems() as $resultItem){
				$currentPopularity = $resultItem->getData('popularity') ?? 0;
				$updatedPopularity = $currentPopularity + 1;
				$resultItem->setData('popularity', $updatedPopularity);
				$this->productResourceModel->saveAttribute($resultItem, 'popularity');
			}

		} // End foreach order item
	}
}
