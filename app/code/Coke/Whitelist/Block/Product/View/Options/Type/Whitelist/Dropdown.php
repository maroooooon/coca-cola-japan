<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Coke\Whitelist\Block\Product\View\Options\Type\Whitelist;

use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Magento\Catalog\Block\Product\View\Options\AbstractOptions;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Pricing\Price\CalculateCustomOptionCatalogRule;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\StoreManager;

/**
 * Represent needed logic for dropdown and multi-select
 */
class Dropdown extends AbstractOptions
{
    /**
     * @var CollectionFactory
     */
    private $whitelistCollectionFactory;
    /**
     * @var StoreManager
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = [],
        CalculateCustomOptionCatalogRule $calculateCustomOptionCatalogRule = null,
        CalculatorInterface $calculator = null,
        PriceCurrencyInterface $priceCurrency = null,
        CollectionFactory $whitelistCollectionFactory,
        StoreManager $storeManager
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data, $calculateCustomOptionCatalogRule, $calculator, $priceCurrency);
        $this->whitelistCollectionFactory = $whitelistCollectionFactory;
        $this->storeManager = $storeManager;
    }


    /**
     * @inheritdoc
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $option = $this->getOption();
        $optionType = $option->getType();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());
        $require = $option->getIsRequire() ? ' required' : '';
        $extraParams = '';
        /** @var Select $select */
        $select = $this->getLayout()->createBlock(
            Select::class
        )->setData(
            [
                'id' => 'select_' . $option->getId(),
                'class' => $require . ' product-custom-option admin__control-select whitelist-dropdown whitelist-input'
            ]
        );
        $select = $this->insertSelectOption($select, $option);
        $select = $this->processSelectOption($select, $option);
        $extraParams .= ' data-type="' . $option->getWhitelistTypeId() . '"';
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        $select->setExtraParams($extraParams);
        if ($configValue) {
            $select->setValue($configValue);
        }
        return $select->getHtml();
    }

    /**
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _toJson()
    {
        $option = $this->getOption();
        return json_encode($this->getOptionValues($option->getWhitelistTypeId()));
    }

    /**
     * Returns select with inserted option give as a parameter
     *
     * @param Select $select
     * @param Option $option
     * @return Select
     */
    private function insertSelectOption(Select $select, Option $option): Select
    {
        $select->setName('options[' . $option->getId() . ']')->addOption('', __('-- Please Select --'));
        return $select;
    }

    /**
     * Returns select with formated option prices
     *
     * @param Select $select
     * @param Option $option
     * @return Select
     */
    private function processSelectOption(Select $select, Option $option): Select
    {
        $store = $this->getProduct()->getStore();
        foreach ($this->getOptionValues($option->getWhitelistTypeId()) as $_value) {
            $select->addOption(
                $_value,
                $_value
            );
        }

        return $select;
    }

    public function getOptionValues($whiteListTypeId)
    {
        $collection = $this->whitelistCollectionFactory->create();
        $collection
            ->addFieldToSelect(['value'])
            ->addFilter('type_id', $whiteListTypeId)
            ->addFilter('status', WhitelistStatus::APPROVED)
            ->addFilter('store_id',  $this->storeManager->getStore()->getId());

        return $collection->load()->getColumnValues('value');
    }
}
