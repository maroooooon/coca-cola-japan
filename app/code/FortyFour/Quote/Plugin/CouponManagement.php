<?php

namespace FortyFour\Quote\Plugin;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagement
{
    const XML_PATH_SHOW_COUPON_ERROR_FOR_GUEST = 'coke_customer/coupon_error_section/show_coupon_error_for_guest';
    const XML_PATH_SHOW_COUPON_ERROR_MESSAGE_FOR_GUEST = 'coke_customer/coupon_error_section/coupon_error_for_guest_message';
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    protected $coupon;

    protected $saleRule;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /*
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        CustomerSession $customerSession,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\Rule $saleRule,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->coupon = $coupon;
        $this->saleRule = $saleRule;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
    /**
     * @inheritDoc
     */
    public function beforeSet(\Magento\Quote\Model\CouponManagement $subject, $cartId, $couponCode)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isShowError = $this->scopeConfig->getValue(self::XML_PATH_SHOW_COUPON_ERROR_FOR_GUEST, $storeScope);
        $couponErrorMessage = $this->scopeConfig->getValue(self::XML_PATH_SHOW_COUPON_ERROR_MESSAGE_FOR_GUEST, $storeScope);
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quote->setCouponCode($couponCode);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('The coupon code couldn\'t be applied: ' .$e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The coupon code couldn't be applied. Verify the coupon code and try again."),
                $e
            );
        }
        /**
         * Check if coupon code requries login and guest user uses it
         */
        if ($quote->getCouponCode() != $couponCode) {
            if($isShowError) {
                $ruleId = $this->coupon->loadByCode($couponCode)->getRuleId();
                if ($ruleId) {
                    $rule = $this->saleRule->load($ruleId);
                    $ruleWebsiteIds = $rule->getWebsiteIds();
                    $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
                    if (in_array($currentWebsiteId, $ruleWebsiteIds)) {
                        $ruleCustomerGroupId = $rule->getCustomerGroupIds();
                        if ((!in_array("0", $ruleCustomerGroupId)) && (!$this->customerSession->isLoggedIn())) {
                            throw new NoSuchEntityException(__($couponErrorMessage));
                        }
                    }
                }
            }
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }
        return true;
    }
}
