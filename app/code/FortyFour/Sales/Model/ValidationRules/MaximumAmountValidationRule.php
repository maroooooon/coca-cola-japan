<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace FortyFour\Sales\Model\ValidationRules;

use FortyFour\Sales\Helper\Config;
use FortyFour\Sales\Helper\ValidateMaximumAmount;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ValidationRules\QuoteValidationRuleInterface;

/**
 * @inheritdoc
 */
class MaximumAmountValidationRule implements QuoteValidationRuleInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;
    /**
     * @var ValidateMaximumAmount
     */
    private $validateMaximumAmount;
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param ValidateMaximumAmount $validateMaximumAmount
     * @param Config $configHelper
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        ValidateMaximumAmount $validateMaximumAmount,
        Config $configHelper
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->validateMaximumAmount = $validateMaximumAmount;
        $this->configHelper = $configHelper;
    }

    /**
     * @param Quote $quote
     * @return array
     */
    public function validate(Quote $quote): array
    {
        $validationErrors = [];
        $validationResult = $this->validateMaximumAmount->validate($quote);
        if (!$validationResult) {
            $validationErrors = [__($this->configHelper->getMaximumOrderErrorMessage())];
        }

        return [$this->validationResultFactory->create(['errors' => $validationErrors])];
    }
}
