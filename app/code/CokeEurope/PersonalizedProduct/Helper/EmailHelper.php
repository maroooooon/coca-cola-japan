<?php

namespace CokeEurope\PersonalizedProduct\Helper;

use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use CokeEurope\PersonalizedProduct\Helper\Config;

class EmailHelper extends AbstractHelper
{
	public const APPROVED_MESSAGE = 'approved';
	public const REJECTED_MESSAGE = 'rejected';

	protected TransportBuilder $transportBuilder;
	protected StoreManagerInterface $storeManager;
	protected StateInterface $inlineTranslation;
	protected Renderer $addressRenderer;
	private Config $config;

	public function __construct(
		Context               $context,
		TransportBuilder      $transportBuilder,
		StoreManagerInterface $storeManager,
		StateInterface        $inlineTranslation,
		Renderer              $addressRenderer,
		Config $config
	)
	{
		parent::__construct($context);
		$this->transportBuilder = $transportBuilder;
		$this->storeManager = $storeManager;
		$this->inlineTranslation = $inlineTranslation;
		$this->addressRenderer = $addressRenderer;
		$this->config = $config;
	}

	/**
	 * It sends an email to the customer with the status of the order and the reason for rejection if the order was rejected
	 *
	 * @param string $status The status of the order.
	 * @param Order $order The order object
	 * @param array $rejection
	 */
	public function sendEmail(string $status, Order $order, array $rejection = [])
	{
		$messageItems = [];
		$configEmailParams = [];

		$items = $order->getItems();
		foreach ($items as $item) {
			$messages = $item->getProductOptionByCode('options');
			if ($messages) {
				$messageItems[] = implode(' - ', [
					$messages[0]['print_value'],
					$messages[1]['print_value']
				]);
			}
		}

		try {
			if ($status === self::APPROVED_MESSAGE) {
				$configEmailParams = $this->config->getApprovedEmailConfigs($order->getStoreId());
			} else {
				$configEmailParams = $this->config->getRejectedEmailConfigs($order->getStoreId());
			}
		} catch (\Exception $e) {
			$this->_logger->info('Cannot set email parameters. Please check admin store configs: '.$e->getMessage());
		}

		$templateId = $configEmailParams['template_id'];
		$fromEmail = $configEmailParams['from_email'];
		$fromName = $configEmailParams['from_name'];
		$toEmail = $order->getCustomerEmail();

		// template variables pass here
		$templateVars = [
			'order' => $order,
			'billing' => $order->getBillingAddress(),
			'store' => $order->getStore(),
			'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
			'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
			'created_at_formatted' => $order->getCreatedAtFormatted(2),
			'order_data' => [
				'customer_name' => $order->getCustomerName(),
				'is_not_virtual' => $order->getIsNotVirtual(),
				'email_customer_note' => $order->getEmailCustomerNote(),
				'frontend_status_label' => $order->getFrontendStatusLabel(),
				'moderatedMessages' => implode('<br/>',$messageItems),
				'rejectionReason' => $rejection ? $rejection['reason'] : "",
				'rejectionExplanation' => $rejection ? $rejection['explanation'] : "",
                'contact_form_url' => $this->config->getContactFormUrl($order->getStoreId())
			]
		];
		$storeId = $order->getStoreId();

		$from = ['email' => $fromEmail, 'name' => $fromName];
		$this->inlineTranslation->suspend();

		$templateOptions = [
			'area' => Area::AREA_FRONTEND,
			'store' => $storeId
		];

		try {
			$transport = $this->transportBuilder->setTemplateIdentifier($templateId)
				->setTemplateOptions($templateOptions)
				->setTemplateVars($templateVars)
				->setFromByScope($from)
				->addTo($toEmail)
				->getTransport();
			$transport->sendMessage();
			$this->inlineTranslation->resume();
		} catch (\Exception $e) {
			$this->_logger->info('Error trying to send the email: '. $e->getMessage());
		}
	}

	/**
	 * It returns the formatted shipping address of the order
	 * @param Order $order The order object
	 * @return string The shipping address of the order.
	 */
	protected function getFormattedShippingAddress($order)
	{
		if ($order->getShippingAddress()) {
			return $this->addressRenderer->format($order->getShippingAddress(), 'html');
		}
		return null;
	}


	/**
	 * It returns the formatted billing address of the order
	 * @param Order $order The order object
	 * @return string The billing address of the order.
	 */
	protected function getFormattedBillingAddress($order)
	{
		return $this->addressRenderer->format($order->getBillingAddress(), 'html');
	}
}
