<?php

namespace CokeJapan\BottledCola\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use CokeJapan\BottledCola\Helper\Config;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

class CartItemAdd implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @param Config $helper
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Config $helper,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory)
    {
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Event
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $product = $observer->getEvent()->getData('product');
            $bundledControlsSku = $this->helper->getBundledSku();
            if ($bundledControlsSku != $product->getSku()) {
                $skuProduct = $product->getData('sku');
                $this->setcookieProduct($skuProduct);
            }
        }
    }

    /**
     * Set cookie product sku
     *
     * @param string $sku
     * @return true
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setcookieProduct($sku)
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDuration($this->helper->getCookieLifetime());
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);
        $this->cookieManager->setPublicCookie('productSku', $sku , $publicCookieMetadata);

        return true;
    }
}
