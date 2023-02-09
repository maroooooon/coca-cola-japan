<?php
/**
 * Copyright © Bounteous All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\PersonalizedProduct\Observer\Frontend\Controller;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

class ActionPredispatchCatalogProductView implements ObserverInterface
{
	/**
	 * @var Http
	 */
	private Http $redirect;
	/**
	 * @var Configurable
	 */
	private Configurable $configurable;
	/**
	 * @var ProductRepositoryInterface
	 */
	private ProductRepositoryInterface $productRepository;
	/**
	 * @var Config
	 */
	private Config $configHelper;

    /**
     * ActionPredispatchCatalogProductView constructor.
     * @param Http $redirect
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepository
     * @param Config $configHelper
     */
	public function __construct(
		Http $redirect,
		Configurable $configurable,
		ProductRepositoryInterface $productRepository,
		Config $configHelper
	)
	{
		$this->redirect = $redirect;
		$this->configurable = $configurable;
		$this->productRepository = $productRepository;
		$this->configHelper = $configHelper;
	}


	/**
	 * Execute observer
	 *
	 * @param Observer $observer
	 * @return void
	 * @throws NoSuchEntityException
	 */
	public function execute(
		Observer $observer
	) {
        $enabled = $this->configHelper->isEnabled();
        $configSku = $this->configHelper->getConfigurableSku();
        $productId = $observer->getEvent()->getRequest()->getParam('id');

        /* Skip if there isn't an id param or configurable sku set */
		if ( !$enabled || !$configSku || !$productId) {
			return;
		}

		/** @var Product $product */
		$product = $this->productRepository->getById($productId);
		if(!$product->getData('prefilled_message') || $product->getSku() === $configSku) return;

		// Redirect to main product with url params if the product has a prefilled_message.
		$personalizedProduct = $this->productRepository->get($configSku);
		$urlWithParams = $personalizedProduct->getUrlModel()->getUrl($personalizedProduct, ['_query' => [
			'brand_swatch' => $product->getData('brand_swatch'),
			'package_bev_type' => $product->getData('package_bev_type'),
			'pattern' => $product->getData('pattern'),
			'prefilled_message' => $product->getData('prefilled_message')
		]]);
		$this->redirect->setRedirect($urlWithParams, 301);
	}
}

