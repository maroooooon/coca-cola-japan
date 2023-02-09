<?php

namespace FortyFour\Catalog\Cron;

use FortyFour\Catalog\Model\TotalQtyOrderedAggregator;

class AggregateTotalOrderedCron
{
    /**
     * @var TotalQtyOrderedAggregator
     */
    private $totalQtyOrderedAggregator;

    /**
     * @param TotalQtyOrderedAggregator $totalQtyOrderedAggregator
     */
    public function __construct(
        TotalQtyOrderedAggregator $totalQtyOrderedAggregator
    ) {
        $this->totalQtyOrderedAggregator = $totalQtyOrderedAggregator;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->totalQtyOrderedAggregator->insertTotalQtyOrdered();
    }
}
