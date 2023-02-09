<?php

namespace CokeEurope\Tax\Block\Total;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use CokeEurope\Tax\Helper\Config;

class SugarTax extends Template
{
    private Config $taxConfig;
    protected $order;
    protected $source;

    public function __construct(
        Context $context,
        Config $taxConfig,
        array $data = []
    )
    {
        $this->taxConfig = $taxConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get data (totals) source model
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Initialize all order totals relates with sugar tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $order = $this->order->load($this->order->getId());

        if (!$this->taxConfig->isEnabled((int) $order->getStoreId())){
            return $this;
        }

        $sugarTaxTotal = $this->taxConfig->getTotalItemsSugarTax($order);
        if ($sugarTaxTotal > 0) {
            $charges = new \Magento\Framework\DataObject(
                [
                    'code' => 'sugar_tax',
                    'strong' => false,
                    'value' => $sugarTaxTotal,
                    'label' => __('Sugar Tax'),
                ]
            );
            $parent->addTotalBefore($charges, 'shipping');
        }
        return $this;
    }
}
