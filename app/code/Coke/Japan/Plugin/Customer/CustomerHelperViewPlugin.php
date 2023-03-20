<?php

namespace Coke\Japan\Plugin\Customer;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerHelperViewPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * CheckoutLayoutPlugin constructor.
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        Escaper $escaper
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    /**
     * @param View $subject
     * @param $result
     * @param CustomerInterface $customerData
     * @return array|mixed|string|void
     */
    public function afterGetCustomerName(View $subject, $result, CustomerInterface $customerData)
    {
        try {
            if ($this->storeManager->getStore()->getWebsite()->getCode() != \Coke\Japan\Model\Website::MARCHE) {
                return $result;
            }

            return $this->escaper->escapeHtml(
                __('%1 %2', $customerData->getLastname(), $customerData->getFirstname())->render()
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->info(__('[CustomerHelperViewPlugin] %1', $e->getMessage()));
        }

    }
}
