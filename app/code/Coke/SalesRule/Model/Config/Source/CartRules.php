<?php

namespace Coke\SalesRule\Model\Config\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\SalesRule\Api\RuleRepositoryInterface;

class CartRules extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $options;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        SearchCriteriaBuilder   $searchCriteriaBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ruleRepository = $ruleRepository;
    }

    public function getAllOptions()
    {
        if ($this->options === null) {
            $this->options = [];

            $this->searchCriteriaBuilder->addFilter('uses_per_customer', 1);

            $searchCriteria = $this->searchCriteriaBuilder->create();

            $rules = $this->ruleRepository->getList($searchCriteria)->getItems();

            $this->options[] = [
                'value' => '',
                'label' => __('-- Select Cart Rule --')
            ];

            foreach ($rules as $rule) {
                $this->options[] = [
                    'value' => $rule->getRuleId(),
                    'label' => $rule->getName()
                ];
            }
        }

        return $this->options;
    }

}
