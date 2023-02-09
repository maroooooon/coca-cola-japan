<?php

namespace FortyFour\Sales\Plugin\Checkout\Block\Onepage;

use FortyFour\Sales\Helper\ValidateMaximumAmount;
use Magento\Checkout\Model\Session;

class Link
{
    /**
     * @var ValidateMaximumAmount
     */
    private $validateMaximumAmount;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Link constructor.
     * @param ValidateMaximumAmount $validateMaximumAmount
     * @param Session $checkoutSession
     */
    public function __construct(
        ValidateMaximumAmount $validateMaximumAmount,
        Session $checkoutSession
    ) {
        $this->validateMaximumAmount = $validateMaximumAmount;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Block\Onepage\Link $subject
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterIsDisabled(
        \Magento\Checkout\Block\Onepage\Link $subject,
        $result
    ) {
        if ($result) {
            return $result;
        }

        return !$this->validateMaximumAmount->validate(
            $this->checkoutSession->getQuote()
        );
    }
}