<?php

namespace FortyFour\SalesRule\Plugin;

use FortyFour\SalesRule\Model\Rule;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class PopulateRuleItemsTotalsInfo
{
    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $ruleRepository;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;
    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    private $validator;
    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    private $validatorUtility;
    /**
     * @var array
     */
    private $_initedRules = [];
    /**
     * @var array
     */
    private $_rulesItemTotals = [];
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var array
     */
    private $items;

    /**
     * @inheritDoc
     */
    public function __construct(
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Utility $validatorUtility,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory
    )
    {
        $this->ruleRepository = $ruleRepository;
        $this->checkoutSession = $checkoutSession;
        $this->validator = $validator;
        $this->validatorUtility = $validatorUtility;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @param \Magento\SalesRule\Model\Validator $subject
     * @param $result
     * @param $items
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    public function afterInitTotals(
        \Magento\SalesRule\Model\Validator $subject,
        $result,
        $items,
        \Magento\Quote\Model\Quote\Address $address
    )
    {
        $this->items = $items;
        unset($this->_initedRules);
        return $result;
    }

    /**
     * @param \Magento\SalesRule\Model\Validator $subject
     * @param \Closure $proceed
     * @param $key
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Validate_Exception
     */
    public function aroundGetRuleItemTotalsInfo(
        \Magento\SalesRule\Model\Validator $subject,
        \Closure $proceed,
        $key
    ){

        $rule = $this->ruleRepository->getById($key);
        if ($rule->getSimpleAction() == Rule::BY_PERCENT_OR_MAX_ALLOWED_AMOUNT) {
            if (!isset($this->_initedRules[$rule->getRuleId()])) {
                $ruleTotalItemsPrice = 0;
                $ruleTotalBaseItemsPrice = 0;
                $validItemsCount = 0;
                $ruleModel = $this->ruleFactory->create()->load($rule->getRuleId());
                $items = $this->items ?? [];
                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if (!$this->isValidItemForRule($item, $ruleModel)) {
                        continue;
                    }
                    $qty = $this->validatorUtility->getItemQty($item, $ruleModel);
                    $ruleTotalItemsPrice += $subject->getItemPrice($item) * $qty;
                    $ruleTotalBaseItemsPrice += $subject->getItemBasePrice($item) * $qty;
                    $validItemsCount++;
                }

                $this->_rulesItemTotals[$rule->getRuleId()] =  [
                    'items_price' => $ruleTotalItemsPrice,
                    'base_items_price' => $ruleTotalBaseItemsPrice,
                    'items_count' => $validItemsCount,
                ];
                $this->_initedRules[$rule->getRuleId()] = true;
            }
            return $this->_rulesItemTotals[$rule->getRuleId()];

        }
        return $proceed($key);
    }

    /**
     * @param \Magento\SalesRule\Model\Validator $subject
     * @param \Closure $proceed
     * @param $key
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundDecrementRuleItemTotalsCount(
        \Magento\SalesRule\Model\Validator $subject,
        \Closure $proceed,
        $key
    ){

        $rule = $this->ruleRepository->getById($key);
        if ($rule->getSimpleAction() == Rule::BY_PERCENT_OR_MAX_ALLOWED_AMOUNT) {
            $this->_rulesItemTotals[$key]['items_count']--;
        } else {
            $proceed($key);
        }
        return $subject;
    }

    /**
     * @param AbstractItem $item
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    private function isValidItemForRule(AbstractItem $item, \Magento\SalesRule\Model\Rule $rule)
    {
        if ($item->getParentItemId()) {
            return false;
        }
        if ($item->getParentItem()) {
            return false;
        }
        if (!$rule->getActions()->validate($item)) {
            return false;
        }
        if (!$this->validator->canApplyDiscount($item)) {
            return false;
        }
        return true;
    }
}
