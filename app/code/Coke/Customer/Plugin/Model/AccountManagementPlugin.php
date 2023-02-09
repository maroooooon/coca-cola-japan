<?php

namespace Coke\Customer\Plugin\Model;

use Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Order\WelcomeEmailSender;
use Magento\Catalog\Model\Config\Source\Price\Scope;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\CouponGenerator;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class AccountManagementPlugin
{
    const MARKETING_CAMPAIGN_CART_RULE = 'coke_customer/marketing/marketing_campaign_cart_rule';
    const MARKETING_CAMPAIGN_COUPON_LENGTH = 'coke_customer/marketing/coupon_code_length';
    const MARKETING_CAMPAIGN_COUPON_PREFIX = 'coke_customer/marketing/coupon_code_prefix';
    const UTM_CAMPAIGN_CODE = 'coke_customer/marketing/utm_campaign_code';

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;
    /**
     * @var CouponGenerator
     */
    private $couponGenerator;
    /**
     * @var CustomerExtensionFactory
     */
    private $customerExtensionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var WelcomeEmailSender
     */
    private $welcomeEmailSender;

    /**
     * AccountManagementPlugin constructor.
     * @param CookieManagerInterface $cookieManager
     * @param ScopeConfigInterface $scopeConfig
     * @param RuleRepositoryInterface $ruleRepository
     * @param CouponGenerator $couponGenerator
     * @param CustomerExtensionFactory $customerExtensionFactory
     * @param LoggerInterface $logger
     * @param WelcomeEmailSender $welcomeEmailSender
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        ScopeConfigInterface $scopeConfig,
        RuleRepositoryInterface $ruleRepository,
        CouponGenerator $couponGenerator,
        CustomerExtensionFactory $customerExtensionFactory,
        LoggerInterface $logger,
        WelcomeEmailSender $welcomeEmailSender
    ){
        $this->cookieManager = $cookieManager;
        $this->scopeConfig = $scopeConfig;
        $this->ruleRepository = $ruleRepository;
        $this->couponGenerator = $couponGenerator;
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->logger = $logger;
        $this->welcomeEmailSender = $welcomeEmailSender;
    }

    public function beforeCreateAccountWithPasswordHash(AccountManagement $subject, CustomerInterface $customer, $hash, $redirectUrl = '')
    {
        $cookie = $this->cookieManager->getCookie('utm_campaign');
        $campaign = $this->scopeConfig->getValue(self::UTM_CAMPAIGN_CODE, ScopeInterface::SCOPE_STORES);
        $cartRule = $this->scopeConfig->getValue(self::MARKETING_CAMPAIGN_CART_RULE, ScopeInterface::SCOPE_STORES);
        $couponLength = $this->scopeConfig->getValue(self::MARKETING_CAMPAIGN_COUPON_LENGTH, ScopeInterface::SCOPE_STORES);
        $couponPrefix = $this->scopeConfig->getValue(self::MARKETING_CAMPAIGN_COUPON_PREFIX, ScopeInterface::SCOPE_STORES);

        if (!$campaign || !$cookie || $cookie !== $campaign) {
            return;
        }

        try {
            $marketingRule = $this->ruleRepository->getById($cartRule);
        } catch (\Exception $e) {
            $this->logger->critical(sprintf(
                'Could not load sales rule for marketing campaign during create account: %s',
                $cartRule
            ));
            return;
        }

        $code = $this->couponGenerator->generateCodes([
            'rule_id' => $marketingRule->getRuleId(),
            'qty' => 1,
            'length' => $couponLength,
            'prefix' => $couponPrefix,
        ]);
        $customer->setCustomAttribute('marketing_registration_coupon_code',$code[0]);

        $this->logger->info(sprintf('[CompletedOrderQuestionnaire] Created coupon code %s for %s', $code[0], $customer->getEmail()));
    }

    public function afterCreateAccountWithPasswordHash(AccountManagement $subject, CustomerInterface $result)
    {
        $attr = $result->getCustomAttribute('marketing_registration_coupon_code');
        if (!$attr) {
            return $result;
        }

        if (!$attr->getValue()) {
            return $result;
        }

        $this->welcomeEmailSender->send([
            'couponCode' => $attr->getValue(),
        ], $result->getEmail());

        $this->logger->info(sprintf('[CompletedOrderQuestionnaire] Sending welcome email to %s', $result->getEmail()));

        return $result;
    }
}
