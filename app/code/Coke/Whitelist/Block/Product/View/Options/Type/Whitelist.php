<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Coke\Whitelist\Block\Product\View\Options\Type;

use Coke\Whitelist\Block\Product\View\Options\Type\Whitelist\DropdownFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Catalog\Helper\Data as CatalogHelper;

/**
 * Product options text type block
 *
 * @api
 * @since 100.0.2
 */
class Whitelist extends \Magento\Catalog\Block\Product\View\Options\AbstractOptions
{
    /**
     * @var DropdownFactory
     */
    private $dropdown;

    /**
     * Select constructor.
     * @param Context $context
     * @param Data $pricingHelper
     * @param CatalogHelper $catalogData
     * @param array $data
     * @param DropdownFactory|null $dropdown
     */
    public function __construct(
        Context $context,
        Data $pricingHelper,
        CatalogHelper $catalogData,
        array $data = [],
        DropdownFactory $dropdown = null
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data);
        $this->dropdown = $dropdown ?: ObjectManager::getInstance()->get(DropdownFactory::class);
    }
    /**
     * Returns default value to show in text input
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('options/' . $this->getOption()->getId());
    }

    /**
     * Return html for control element
     *
     * @return string
     */
    public function getValuesHtml(): string
    {
        $option = $this->getOption();
        $optionBlock = $this->dropdown->create();
        return $optionBlock
            ->setOption($option)
            ->setProduct($this->getProduct())
            ->_toHtml();
    }

    public function getValues(): string
    {
        $option = $this->getOption();
        $optionBlock = $this->dropdown->create();
        return $optionBlock
            ->setOption($option)
            ->setProduct($this->getProduct())
            ->_toJson();
    }

}
