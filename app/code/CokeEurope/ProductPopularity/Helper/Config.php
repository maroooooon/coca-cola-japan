<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\ProductPopularity\Helper;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Config extends AbstractHelper
{
	const XML_CONFIG_POPULARITY_ENABLED = 'coke_europe/popularity/enabled';
	private AttributeRepositoryInterface $attributeRepository;
	private LoggerInterface $logger;

	/**
	 * @param Context $context
	 * @param LoggerInterface $logger
	 * @param AttributeRepositoryInterface $attributeRepository
	 */
	public function __construct(
		Context                      $context,
		LoggerInterface              $logger,
		AttributeRepositoryInterface $attributeRepository
	)
	{
		parent::__construct($context);
		$this->logger = $logger;
		$this->attributeRepository = $attributeRepository;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->scopeConfig->isSetFlag(self::XML_CONFIG_POPULARITY_ENABLED, ScopeInterface::SCOPE_STORE);
	}

	public function getPopularityAttributeMap(): array
	{
		$attributes = [];
		$pattern_code = 'pattern';
		$brand_code = 'brand_swatch';
		$message_code = 'prefilled_message';
		try {
			$brand = $this->attributeRepository->get(Product::ENTITY, $brand_code);
			$pattern = $this->attributeRepository->get(Product::ENTITY, $pattern_code);
			$message = $this->attributeRepository->get(Product::ENTITY, $message_code);
			$attributes[$pattern->getAttributeId()] = $pattern_code;
			$attributes[$brand->getAttributeId()] = $brand_code;
			$attributes[$message->getAttributeId()] = $message_code;
		} catch (NoSuchEntityException $e) {
			$this->logger->warning($e);
		}

		return $attributes;
	}

	public function getMessageOptions(): array
	{
		$result = [];
		try {
			$messages = $this->attributeRepository->get(Product::ENTITY, 'prefilled_message');
			foreach ($messages->getOptions() as $opt) {
				if (!$opt->getValue()) {
					continue;
				} else {
					$result[$opt->getLabel()] = $opt->getValue();
				}
			}
		} catch (NoSuchEntityException $e) {
			$this->logger->warning($e);
		}
		return $result;
	}
}

