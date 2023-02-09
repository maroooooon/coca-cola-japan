<?php

namespace FortyFour\Voucher\Model\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;

class SalesRuleId implements OptionSourceInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @return \Magento\SalesRule\Api\Data\RuleInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSalesRules(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Magento\SalesRule\Model\Data\Rule::KEY_IS_ACTIVE, 1)
            ->create();

        return $this->ruleRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $optionArray = [];

        $salesRules = $this->getSalesRules();
        $optionArray[] = ['value' => '', 'label' => __('-- Please Select --')];
        foreach ($salesRules as $salesRule) {
            $optionArray[] = [
                'value' => $salesRule->getRuleId(),
                'label' => $salesRule->getName()
            ];
        }

        return $optionArray;
    }
}
