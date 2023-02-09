<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\PersonalizedProduct\Plugin;

use CokeEurope\PersonalizedProduct\Helper\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;

class AddToCartMessage
{

 	const ATC_SUCCESS_MESSAGE_IDENTIFIER = 'addCartSuccessMessage';
	private Config $configHelper;
	private ManagerInterface $messageManager;
	private ProductRepositoryInterface $productRepository;

	public function __construct(
		Config                     $configHelper,
		ManagerInterface           $messageManager,
		ProductRepositoryInterface $productRepository
	)
	{
		$this->configHelper = $configHelper;
		$this->messageManager = $messageManager;
		$this->productRepository = $productRepository;
	}


	/**
	 * @throws NoSuchEntityException
	 */
	public function afterExecute(
		Add $subject,
			$result
	)
	{
		// Skip if module is not enabled
		if (!$this->configHelper->isEnabled()) {
			return $result;
		}

		$productId = $subject->getRequest()->getParam('product');
		$product = $this->productRepository->getById($productId);

		// Skip if the sku doesn't match personalized product sku
		if ($product->getSku() !== $this->configHelper->getConfigurableSku()) {
			return $result;
		}

		// Change the success message
		$messages = $this->messageManager->getMessages();
		$lastMessage = $messages->getLastAddedMessage()->getIdentifier();
		if ($lastMessage === self::ATC_SUCCESS_MESSAGE_IDENTIFIER) {
			$messages->deleteMessageByIdentifier(self::ATC_SUCCESS_MESSAGE_IDENTIFIER);
			$this->messageManager->addComplexSuccessMessage(
				self::ATC_SUCCESS_MESSAGE_IDENTIFIER,
				[
					'reset_url' => $product->getProductUrl(),
				]
			);
		}

		return $result;
	}
}
