<?php

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\DataObject;
use Magento\Sales\Block\Order\Totals as TotalsBlock;
use Magento\Sales\Model\Order;

/**
 * Class Totals
 */
class Totals extends AbstractHelper
{
    /**
     * @var Order
     */
    protected $source;

    /**
     * @var TotalsBlock
     */
    protected $totalsBlock;

    /**
     * @var array
     */
    protected $totals = [];

    /**
     * @var array
     */
    protected $newTotals = [];

    /**
     * Invoice Totals
     *
     * @var string[]
     */
    protected $invoiceTotals = [
        'subtotal'                  => 'Total TTC',
        'shipping'                  => 'Frais de port TTC',
        'shipping_discount_amount'  => 'Remise de frais de port TTC',
        'discount'                  => 'Remise code promo TTC',
        'grand_total_incl'          => 'Total net TTC',
        'tax'                       => 'TVA',
        'grand_total'               => 'Total HT'
    ];

    /**
     * @param TotalsBlock $totals
     * @return $this
     */
    public function setup(TotalsBlock $totals): Totals
    {
        if (!$this->totalsBlock) {
            $this->totalsBlock = $totals;
        }
        $this->source = $totals->getOrder();

        return $this;
    }

    /**
     * @return array
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function renderTotals(): array
    {
        $initialTotals = $this->totalsBlock->getTotals();
        foreach ($this->invoiceTotals as $code => $label) {
            $total = isset($initialTotals[$code]) ?
                $this->totalsBlock->getTotal($code) : $this->addTotal($code);

            if (!$total->getBlockName()) {
                $total->setLabel($label);
            }
            $this->newTotals[$code] = $total;
        }

        return $this->newTotals;
    }

    /**
     * @return Order
     */
    public function getSource(): Order
    {
        return $this->source;
    }

    /**
     * @param string $code
     * @return DataObject
     */
    public function addTotal(string $code): DataObject
    {
        $price = 0;

        return new DataObject(
            [
                'code' => $code,
                'value' => $this->source->formatPrice($price),
                'label' => $code
            ]
        );
    }
}
