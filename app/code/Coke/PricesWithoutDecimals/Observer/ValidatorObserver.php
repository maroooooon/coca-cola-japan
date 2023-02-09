<?php

namespace Coke\PricesWithoutDecimals\Observer;

use Coke\PricesWithoutDecimals\Helper\Config;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\Data;

/**
 * SalesRule validator observer
 */
class ValidatorObserver implements ObserverInterface
{
    /**
     * @var array
     */
    private $roundRules = [Rule::CART_FIXED_ACTION];

    /**
     * @var array
     */
    private $percentOrMaxAllowedRules = [\FortyFour\SalesRule\Model\Rule::BY_PERCENT_OR_MAX_ALLOWED_AMOUNT];

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * ValidatorObserver constructor.
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    ){
        $this->configHelper = $configHelper;
    }

    /**
     * Rounding calculated discount
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->isShowingDecimals()) {
            /** @var Data $discountData */
            $discountData = $observer->getEvent()->getData('result');
            $rule = $observer->getEvent()->getData('rule');

            if (in_array($rule->getData('simple_action'), $this->roundRules)) {
                $this->setDiscountDataAndRound($discountData);
                return;
            }

            if (in_array($rule->getData('simple_action'), $this->percentOrMaxAllowedRules)) {
                $discountAmount = $rule->getData('discount_amount');
                $maxAllowedDiscount = $rule->getData('max_allowed_discount');

                if ($discountAmount == $maxAllowedDiscount) {
                    $this->setDiscountData($discountData);
                    return;
                }
            }

            $this->setDiscountDataAndCeil($discountData);
        }
    }

    /**
     * @param Data $discountData
     */
    private function setDiscountData(Data $discountData)
    {
        $discountData->setAmount($discountData->getAmount());
        $discountData->setBaseAmount($discountData->getBaseAmount());
        $discountData->setOriginalAmount($discountData->getOriginalAmount());
        $discountData->setBaseOriginalAmount($discountData->getBaseOriginalAmount());
    }

    /**
     * @param Data $discountData
     */
    private function setDiscountDataAndRound(Data $discountData)
    {
        $discountData->setAmount(round($discountData->getAmount()));
        $discountData->setBaseAmount(round($discountData->getBaseAmount()));
        $discountData->setOriginalAmount(round($discountData->getOriginalAmount()));
        $discountData->setBaseOriginalAmount(round($discountData->getBaseOriginalAmount()));
    }

    /**
     * @param Data $discountData
     */
    private function setDiscountDataAndCeil(Data $discountData)
    {
        $discountData->setAmount(ceil($discountData->getAmount()));
        $discountData->setBaseAmount(ceil($discountData->getBaseAmount()));
        $discountData->setOriginalAmount(ceil($discountData->getOriginalAmount()));
        $discountData->setBaseOriginalAmount(ceil($discountData->getBaseOriginalAmount()));
    }
}
