<?php
/**
 * Copyright © bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\DataLayer\ViewModel;

use CokeEurope\DataLayer\Helper\Config as ConfigHelper;
use CokeEurope\DataLayer\Helper\Data as DataHelper;
use CokeEurope\PersonalizedProduct\Helper\Config as PersonalizedProductHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Api\CartRepositoryInterface as QuoteRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;

class Data implements ArgumentInterface
{


	private DataHelper $dataHelper;
	private ConfigHelper $configHelper;
	private SerializerInterface $serializer;
	private CheckoutSession $checkoutSession;
	private QuoteRepository $quoteRepository;
	private Configurable $configurable;
	private LoggerInterface $logger;
	private ProductRepositoryInterface $productRepository;
	private PersonalizedProductHelper $personalizedProductHelper;

	/**
	 * @param DataHelper $dataHelper
	 * @param ConfigHelper $configHelper
	 * @param SerializerInterface $serializer
	 * @param CheckoutSession $checkoutSession
	 * @param QuoteRepository $quoteRepository
	 * @param Configurable $configurable
	 * @param ProductRepositoryInterface $productRepository
	 * @param PersonalizedProductHelper $personalizedProductHelper
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		DataHelper          $dataHelper,
		ConfigHelper        $configHelper,
		SerializerInterface $serializer,
		CheckoutSession     $checkoutSession,
		QuoteRepository     $quoteRepository,
		Configurable 		$configurable,
		ProductRepositoryInterface $productRepository,
		PersonalizedProductHelper $personalizedProductHelper,
		LoggerInterface     $logger
	)
	{

		$this->dataHelper = $dataHelper;
		$this->configHelper = $configHelper;
		$this->serializer = $serializer;
		$this->checkoutSession = $checkoutSession;
		$this->quoteRepository = $quoteRepository;
		$this->configurable = $configurable;
		$this->productRepository = $productRepository;
		$this->personalizedProductHelper = $personalizedProductHelper;
		$this->logger = $logger;
	}

	/**
	 * Get the Google API Key from system config
	 * @return string
	 */
	public function getGtmId(): ?string
	{
		return $this->configHelper->getGtmId();
	}

	/**
	 * Get the config for knockout components
	 * @return string
	 */
	public function getJsonConfig(): string
	{
		return $this->serializer->serialize([
			'enabled' => $this->configHelper->isEnabled(),
			'gtm_id' => $this->configHelper->getGtmId()
		]);
	}

	/**
	 * Get json for productDetail event
	 * @param $product
	 * @param null $options
	 * @return string
	 */
	public function getProductDetail($product, $options = null): ?string
	{
		$productSku = $product->getSku();
		$productName = $product->getName();
		$productPrice = (string) $product->getFinalPrice();
		$productCategory =  $product->getCategory() ?  $product->getCategory()->getName() : 'null';

		// If the product has options set via query string load the configured product data for datalayer
		if ($configuredProduct = $this->dataHelper->getConfiguredProduct($product, $options)) {
			$productSku = $configuredProduct->getSku();
			$productName = $configuredProduct->getName();
			$productPrice = (string) $configuredProduct->getFinalPrice();
			$productCategory = $product->getSku();
		}

		return $this->serializer->serialize([
			'event' => 'productDetail',
			'ecommerce' => [
				'detail' => [
					'products' => [[
						'id' => $productSku,
						'name' => $productName,
						'price' => $productPrice,
						'category' => $productCategory,
						'currencyCode' => $this->dataHelper->getCurrencyCode()
					]]
				]
			]
		]);
	}

	/**
	 * Get simple products for personalized product used to format placeholder product data for datalayer
	 * @return array
	 */
	public function getDatalayerProductMap(): array
	{
		$i = 0;
		$results = [];
		try {
			$personalisedProduct = $this->productRepository->get($this->personalizedProductHelper->getConfigurableSku());
			$childProducts = $personalisedProduct->getTypeInstance()->getUsedProducts($personalisedProduct);
			foreach($childProducts as $child){
				$attributes = $this->configurable->getConfigurableAttributes($personalisedProduct);
				$results[$i] = [
					'sku' => $child->getSku(),
					'name' => $child->getName(),
					'options' => []
				];
				foreach($attributes as $attribute) {
					$attributeCode = $attribute->getProductAttribute()->getAttributeCode();
					$results[$i]['options'][$attributeCode] = (int) $child->getData($attributeCode);
				}
				$i++;
			}
		} catch (NoSuchEntityException $e) {
			$this->logger->info('DataLayer Error: '. $e->getMessage());
		}

		return $results;
	}

	/**
	 * Get json for productImpression event
	 * @param $product
	 * @param $map
	 * @param $position
	 * @return string
	 */
	public function getProductData($product, $map, $position): string
	{
		$brand_swatch = $product->getData('brand_swatch');
		$package_bev_type =  $product->getData('package_bev_type');
		$pattern = $product->getData('pattern');

		// If the product has all 3 personalised product attributes find the matching simple and use its name and sku for datalayer events
		$datalayerSku = null;
		$datalayerName = null;
		if($brand_swatch && $package_bev_type && $pattern) {
			$options = [
				'brand_swatch' => $brand_swatch,
				'package_bev_type' => $package_bev_type,
				'pattern' => $pattern
			];
			if(count($options) === 3) {
				$search = array_column($map, 'options');
				$matchingKey = array_search($options, $search);
				if($matchingKey){
					$datalayerSku = $map[$matchingKey]['sku'];
					$datalayerName = $map[$matchingKey]['name'];
				}
			}
		}

		return $this->serializer->serialize([
			'id' => $datalayerSku ?? $product->getSku(),
			'name' => $datalayerName ?? $product->getName(),
			'category' => $product->getCategory() ? $product->getCategory()->getName() : 'null',
			'price' => (string)$product->getFinalPrice(),
			'position' => (string)$position,
			'currencyCode' => $this->dataHelper->getCurrencyCode()
		]);
	}

	public function getCheckoutProducts()
	{
		$products = [];
		try {
			$items = $this->checkoutSession->getQuote()->getAllVisibleItems();
			foreach ($items as $item) {
				$products[] = $this->serializer->unserialize($this->dataHelper->formatProduct($item));
			}
		} catch (NoSuchEntityException | LocalizedException $e) {
			$this->logger->info('DataLayer Error: '. $e->getMessage());
		}

		return $this->serializer->serialize($products);
	}

	public function getPurchaseEvent()
	{
		$quote = null;
		$event = [];
		$products = [];
		$lastOrder = $this->checkoutSession->getLastRealOrder();
		try {
			$quote = $this->quoteRepository->get($lastOrder->getQuoteId());
		} catch (NoSuchEntityException $e) {
			$this->logger->info('DataLayer Error: '. $e->getMessage());
		}
		if(!$quote){
			return $this->serializer->serialize($event);
		}
		foreach ($quote->getAllVisibleItems() as $item) {
			$products[] = $this->serializer->unserialize($this->dataHelper->formatProduct($item));
		}
		$event = [
			'event' => 'purchase',
			'ecommerce' => [
                'currencyCode' => $this->dataHelper->getCurrencyCode(),
				'purchase' => [
					'actionField' => [
						'id' => $lastOrder->getIncrementId(),
                        'subtotal' => (string) $lastOrder->getSubtotal(),
						'tax' => (string) $lastOrder->getTaxAmount(),
						'shipping' => (string) $lastOrder->getShippingAmount(),
                        'discount' => (string) $lastOrder->getDiscountAmount(),
                        'revenue' => (string) $lastOrder->getGrandTotal(),
                        'totalexcltax' => (string) $lastOrder->getSubtotal(),
                        'totalincltax' => (string) $lastOrder->getSubtotalInclTax(),
                        'paymentmethod' => $lastOrder->getPayment()->getMethod(),
                        'coupon' => $lastOrder->getCouponCode() ?? ''
                    ],
					'products' => $products
				]
			]
		];
		return $this->serializer->serialize($event);
	}

    public function getCurrencyCode(): string
    {
        return $this->dataHelper->getCurrencyCode();
    }
}
