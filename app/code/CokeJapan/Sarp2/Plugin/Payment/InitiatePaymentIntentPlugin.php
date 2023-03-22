<?php

namespace CokeJapan\Sarp2\Plugin\Payment;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2Stripe\Model\Payment\Sampler\RequestDataBuilder\InitiatePaymentIntent;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use StripeIntegration\Payments\Helper\Generic as GenericHelper;

class InitiatePaymentIntentPlugin
{
    const STRIPE_MINIMUM_CHARGE_AMOUNT_JPY = 50;

    /**
     * @var GenericHelper
     */
    private GenericHelper $genericHelper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param GenericHelper $genericHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GenericHelper $genericHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->genericHelper = $genericHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param InitiatePaymentIntent $subject
     * @param $result
     * @param ProfileInterface $profile
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterBuild(
        InitiatePaymentIntent $subject,
        $result,
        ProfileInterface $profile
    ): array
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $currency = $store->getCurrentCurrencyCode();
        $cents = $this->genericHelper->isZeroDecimal($currency) ? 1 : 100;

        if ($result[InitiatePaymentIntent::AMOUNT] < self::STRIPE_MINIMUM_CHARGE_AMOUNT_JPY) {
            $result[InitiatePaymentIntent::AMOUNT] = round($profile->getRegularGrandTotal() * $cents);
        }
        return $result;
    }
}
