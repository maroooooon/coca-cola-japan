<?php

/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\DataLayer\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{


	private LoggerInterface $logger;
	private SerializerInterface $serializer;
	private StoreManagerInterface $storeManager;
	private CookieManagerInterface $cookieManager;
	private CookieMetadataFactory $cookieMetadata;
	private AttributeRepository $attributeRepository;
    private Configurable $configurable;

    public function __construct(
		Context                $context,
		LoggerInterface        $logger,
		SerializerInterface    $serializer,
		StoreManagerInterface  $storeManager,
		CookieManagerInterface $cookieManager,
		CookieMetadataFactory  $cookieMetadata,
		AttributeRepository $attributeRepository,
		Configurable $configurable
	)
	{
		parent::__construct($context);
		$this->serializer = $serializer;
		$this->cookieManager = $cookieManager;
		$this->cookieMetadata = $cookieMetadata;
		$this->logger = $logger;
		$this->storeManager = $storeManager;
		$this->attributeRepository = $attributeRepository;
        $this->configurable = $configurable;
    }

	public function getConfiguredProduct($product, $options): ?Product
	{
		$attributes = [];
		foreach($options as $key => $value) {
			if(!in_array($key, ['brand_swatch', 'package_bev_type', 'pattern'])) {
				continue;
			}
			try {
				$attribute = $this->attributeRepository->get($key);
				$attributes[$attribute->getAttributeId()] = $value;
			} catch (NoSuchEntityException $e) {
				$this->logger->info('DataLayer Error: '. $e->getMessage());
			}
		}
		if(count($attributes) === 3) {
			return $this->configurable->getProductByAttributes($attributes, $product);
		}
		return null;
	}

	public function formatProduct($item)
	{
		$product = $item->getProduct();
		$productName = $product->getName();
		$category = $product->getCategory() ? $product->getCategory()->getName() : 'null';

		if($productName === 'Personalised Product'){
			$category = 'personalised-product';
			if ($simple = $item->getOptionByCode('simple_product')) {
				$productName = $simple->getProduct()->getName();
			}
		}

		$result = [
			'id' => $product->getSku(),
			'name' => $productName,
			'qty' => (int)$item->getQty(),
			'price' => (string) $product->getFinalPrice(),
			'category' => $category
		];



		if ($item->getQty() < 1 && $item->getQtyOrdered()) {
			$result['qty'] = (int)$item->getQtyOrdered();
		}

        // Add applied rule id's
        if($rules = $item->getAppliedRuleIds()) {
            $result['coupon'] = $rules;
        }

		return $this->serializer->serialize($result);
	}

	public function setDatalayerCookie($name, $data, $qtyUpdate = null)
	{
		$metadata = $this->cookieMetadata
			->createPublicCookieMetadata()
			->setPath('/')
			->setSecure(true)
			->setHttpOnly(false)
			->setDuration(3600);

		// Update the qty if applicable
		if ($qtyUpdate) {
			$data = $this->serializer->unserialize($data);
			$data['qty'] = (int)$qtyUpdate;
			$data = $this->serializer->serialize($data);
		}

		try {
			$this->cookieManager->setPublicCookie(
				$name,
				$data,
				$metadata
			);
		} catch (InputException | CookieSizeLimitReachedException | FailureToSendException $e) {
			$this->logger->info('DataLayer Error: '. $e->getMessage());
		}
	}

	public function getDataLayerSectionData(): array
	{
		$result = [];
		$currencyCode = '';
		$cookieAddToCart = $this->cookieManager->getCookie('dl_cart_item_added');
		$cookieRemoveFromCart = $this->cookieManager->getCookie('dl_cart_item_removed');

		try {
			$currencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
		} catch (NoSuchEntityException | LocalizedException $e) {
			$this->logger->info('DataLayer Error: '. $e->getMessage());
		}

		if ($cookieAddToCart) {
			$productFromCookie = $this->serializer->unserialize($cookieAddToCart);
			$addToCartEvent = $this->serializer->serialize([
				'event' => 'addToCart',
				'ecommerce' => [
					'currencyCode' => $currencyCode,
					'add' => [
						'products' => [
							$productFromCookie
						]
					]
				]
			]);
			$result['add_to_cart'] = $addToCartEvent;
		}

		if ($cookieRemoveFromCart) {
			$productFromCookie = $this->serializer->unserialize($cookieRemoveFromCart);
			$removeFromCartEvent = $this->serializer->serialize([
				'event' => 'removeFromCart',
				'ecommerce' => [
					'currencyCode' => $currencyCode,
					'remove' => [
						'products' => [
							$productFromCookie
						]
					]
				]
			]);
			$result['remove_from_cart'] = $removeFromCartEvent;
		}

		return $result;
	}

	public function getCurrencyCode(): string
	{
		$currencyCode = 'GBP';
		try {
			$currencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
		} catch (NoSuchEntityException | LocalizedException $e) {
			$this->logger->info('DataLayer Error: '. $e->getMessage());
		}
		return $currencyCode;
	}

}
