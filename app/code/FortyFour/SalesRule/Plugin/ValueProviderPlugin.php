<?php

namespace FortyFour\SalesRule\Plugin;

use Magento\SalesRule\Model\Rule;
use Psr\Log\LoggerInterface;

class ValueProviderPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddAdditionalSalesRuleApplyAction constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param Rule\Metadata\ValueProvider $subject
     * @param $result
     * @param Rule $rule
     * @return mixed
     */
    public function afterGetMetadataValues(
        \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $subject,
        $result,
        \Magento\SalesRule\Model\Rule $rule
    ) {
        $applyOptions[] = [
            'label' => __('Percent of product price or max allowed discount'),
            'value' => \FortyFour\SalesRule\Model\Rule::BY_PERCENT_OR_MAX_ALLOWED_AMOUNT
        ];

        $result['actions']['children']['simple_action']['arguments']['data']['config']['options'] = array_merge(
            $result['actions']['children']['simple_action']['arguments']['data']['config']['options'],
            $applyOptions
        );

        return $result;
    }
}
