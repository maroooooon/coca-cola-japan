<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CokeEurope\Checkout\ViewModel\Success;

use CokeEurope\Tax\Helper\Config as TaxConfig;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Checkout\Model\Session;
use CokeEurope\Catalog\Api\Data\ModerationStatusInterface;

class Additional implements ArgumentInterface
{

	private ImageHelper $imageHelper;
	private PriceHelper $priceHelper;
	private Session $checkoutSession;
    private TaxConfig $taxConfig;

	public function __construct(
		PriceHelper $priceHelper,
		ImageHelper $imageHelper,
		Session $checkoutSession,
        TaxConfig $taxConfig
	)
	{
		$this->priceHelper = $priceHelper;
		$this->imageHelper = $imageHelper;
		$this->checkoutSession = $checkoutSession;
        $this->taxConfig = $taxConfig;
	}

	/**
	 * Get order from checkout session
	 * @return Order
	 */
	public function getOrder(): Order
	{
		return $this->checkoutSession->getLastRealOrder();
	}

	/**
	 * Get order item image url
	 * @param $item
	 * @return string
	 */
	public function getImageUrlFromItem($item): string
	{
		$image = $this->imageHelper->init($item, 'cart_page_product_thumbnail');
		return $image->getUrl();
	}

	/**
	 * Get formatted order item price
	 * @param $item
	 * @return float|string
	 */
	public function getUnitPrice($item)
	{
		$price = $item->getPrice();
		return $this->priceHelper->currency($price, true, false);
	}

	/**
	 * Format currency
	 * @param $amount
	 * @return float|string
	 */
	public function formatCurrency($amount)
	{
		return $this->priceHelper->currency($amount, true, false);
	}


	/**
	 * Get order item moderation status
	 * @param $item
	 * @return mixed|string|void|null
	 */
	public function getModerationStatus($item)
	{
		$status = (int) $item->getModerationStatus();
		if($status === ModerationStatusInterface::MODERATION_STATUS_PENDING) {
			return _("Pending Approval");
		}
		return null;
	}

    /**
     * This function returns the total sugar tax for an order
     *
     * @param Order $order The order object
     * @return float The total sugar tax for all items in the order.
     */
    public function getSugarTaxTotal(Order $order): float {
        return $this->taxConfig->getTotalItemsSugarTax($order);
    }
}
