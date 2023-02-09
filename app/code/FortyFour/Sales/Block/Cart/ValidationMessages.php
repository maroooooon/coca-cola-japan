<?php

namespace FortyFour\Sales\Block\Cart;

use FortyFour\Sales\Helper\Config;
use FortyFour\Sales\Helper\ValidateMaximumAmount;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

class ValidationMessages extends \Magento\Checkout\Block\Cart\ValidationMessages
{
    /**
     * @var Config
     */
    private $configHelper;
    /**
     * @var ValidateMaximumAmount
     */
    private $validateMaximumAmount;

    /**
     * ValidationMessages constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param InterpretationStrategyInterface $interpretationStrategy
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\Locale\CurrencyInterface $currency
     * @param Config $configHelper
     * @param ValidateMaximumAmount $validateMaximumAmount
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        Config $configHelper,
        ValidateMaximumAmount $validateMaximumAmount,
        array $data = []
    ) {
        parent::__construct($context, $messageFactory, $collectionFactory, $messageManager, $interpretationStrategy, $cartHelper, $currency, $data);
        $this->configHelper = $configHelper;
        $this->validateMaximumAmount = $validateMaximumAmount;
    }

    /**
     * @return \Magento\Checkout\Block\Cart\ValidationMessages
     */
    protected function _prepareLayout()
    {
        if ($this->cartHelper->getItemsCount()) {
            $this->validateMaximumAmount();
        }

        return parent::_prepareLayout();
    }

    /**
     * Validate minimum amount and display notice in error
     *
     * @return void
     */
    protected function validateMaximumAmount()
    {
        if (!$this->validateMaximumAmount->validate($this->cartHelper->getQuote())) {
            $this->messageManager->addNoticeMessage(__($this->configHelper->getMaximumOrderErrorMessage()));
        }
    }
}
