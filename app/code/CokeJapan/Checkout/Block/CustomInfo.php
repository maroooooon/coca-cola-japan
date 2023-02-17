<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CokeJapan\Checkout\Block;

use Magento\Customer\Block\Form\Login\Info;
/**
 * Customer login info block
 *
 * @api
 * @since 100.0.2
 */
class CustomInfo extends Info
{
    /**
     * @var \Magento\Checkout\Block\Cart
     */
    protected $_checkoutcart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutHelper;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Registration $registration
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Framework\Url\Helper\Data $coreUrl
     * @param \Magento\Checkout\Block\Cart $checkoutcart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        \Magento\Checkout\Block\Cart $checkoutcart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        array $data = []
    ) {
        parent::__construct($context, $registration, $customerUrl, $checkoutData, $coreUrl, $data);
        $this->registration = $registration;
        $this->_customerUrl = $customerUrl;
        $this->checkoutData = $checkoutData;
        $this->coreUrl = $coreUrl;
        $this->_checkoutcart = $checkoutcart;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_urlBuilder = $context->getUrlBuilder();

    }

    public function hasErrorCheckout()
    {
        return $this->_checkoutcart->hasError();
    }

    public function getMethodsCheckout($customData)
    {
        return $this->_checkoutcart->getMethods($customData);
    }

    public function getMethodHtmlCheckout($customHTML)
    {
        return $this->_checkoutcart->getMethodHtml($customHTML);
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->_urlBuilder->getUrl('checkout', []);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->_checkoutSession->getQuote()->validateMinimumAmount();
    }

    /**
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        return $this->_checkoutHelper->canOnepageCheckout();
    }
}
