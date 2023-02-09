<?php

namespace Coke\Whitelist\Observer;

use Coke\Whitelist\Model\ModuleConfig;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Helper\Product\Configuration as ProductConfigurationHelper;

class SalesQuoteRemoveItem implements ObserverInterface
{
    /** @var Session */
    protected Session $checkoutSession;

    /** @var ModuleConfig */
    protected ModuleConfig $config;

    /** @var ProductConfigurationHelper */
    protected ProductConfigurationHelper $helper;

    /**
     * Constructor
     *
     * @param Session                    $checkoutSession
     * @param ModuleConfig               $config
     * @param ProductConfigurationHelper $helper
     */
    public function __construct(Session $checkoutSession, ModuleConfig $config, ProductConfigurationHelper $helper)
    {

        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isRestrictionEnabled()) {
            $isCartPending = false;

            $items = $this->checkoutSession->getQuote()->getAllItems();
            foreach ($items as $item) {
                $parent = $item->getParentItem();
                if (isset($parent) && !empty($parent)) {
                    $options = $this->helper->getCustomOptions($parent);

                    foreach ($options as $option) {
                        if (isset($option['whitelist_status_pending']) && !empty($option['whitelist_status_pending'])) {
                            $isCartPending = true;

                            break;
                        }
                    }
                }
            }

            $status = null;
            if ($isCartPending) {
                $status = 1;
            }

            $this->checkoutSession->getQuote()->setData('whitelist_status_pending', $status);
        }
    }
}
