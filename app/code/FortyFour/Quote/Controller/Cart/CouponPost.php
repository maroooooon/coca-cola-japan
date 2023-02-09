<?php

namespace FortyFour\Quote\Controller\Cart;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class CouponPost extends \Magento\Checkout\Controller\Cart\CouponPost implements HttpPostActionInterface
{
    const XML_PATH_SHOW_COUPON_ERROR_FOR_GUEST = 'coke_customer/coupon_error_section/show_coupon_error_for_guest';
    const XML_PATH_SHOW_COUPON_ERROR_MESSAGE_FOR_GUEST = 'coke_customer/coupon_error_section/coupon_error_for_guest_message';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $saleRule;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\SalesRule\Model\Rule $saleRule
     * @param CustomerSession $customerSession
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\SalesRule\Model\Rule $saleRule,
        CustomerSession $customerSession
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $couponFactory,
            $quoteRepository
        );
        $this->scopeConfig = $scopeConfig;
        $this->saleRule = $saleRule;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }
    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                } else {
                    if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                        $isShowError = $this->scopeConfig->getValue(self::XML_PATH_SHOW_COUPON_ERROR_FOR_GUEST, $storeScope);
                        $couponErrorMessage = $this->scopeConfig->getValue(self::XML_PATH_SHOW_COUPON_ERROR_MESSAGE_FOR_GUEST, $storeScope);
                        if($isShowError) {
                            $coupon = $this->couponFactory->create();
                            $coupon->load($couponCode, 'code');
                            $ruleId = $coupon->getRuleId();
                            if ($ruleId) {
                                $rule = $this->saleRule->load($ruleId);
                                $ruleWebsiteIds = $rule->getWebsiteIds();
                                $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
                                if (in_array($currentWebsiteId, $ruleWebsiteIds)) {
                                    $ruleCustomerGroupId = $rule->getCustomerGroupIds();
                                    if ((!in_array("0", $ruleCustomerGroupId)) && (!$this->customerSession->isLoggedIn())) {
                                        $this->messageManager->addErrorMessage(__($couponErrorMessage));
                                        return $this->_goBack();
                                    }
                                }
                            }
                        }
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                }
            } else {
                $this->messageManager->addSuccessMessage(__('You canceled the coupon code.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot apply the coupon code.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_goBack();
    }
}
