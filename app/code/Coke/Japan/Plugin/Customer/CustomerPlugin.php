<?php

namespace Coke\Japan\Plugin\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerPlugin
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
     * @param Customer $subject
     * @param $result
     * @return array|mixed|string|void
     */
    public function afterGetName(Customer $subject, $result)
    {
        try {
            if ($this->storeManager->getStore()->getWebsite()->getCode() != \Coke\Japan\Model\Website::MARCHE) {
                return $result;
            }

            return $this->escaper->escapeHtml(
                __('%1 %2', $subject->getLastname(), $subject->getFirstname())
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->info(__('[CustomerHelperViewPlugin] %1', $e->getMessage()));
        }

    }
}

