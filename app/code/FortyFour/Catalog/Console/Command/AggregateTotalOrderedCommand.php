<?php

namespace FortyFour\Catalog\Console\Command;

use FortyFour\Catalog\Model\TotalQtyOrderedAggregator;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AggregateTotalOrderedCommand extends Command
{
    /**
     * @var TotalQtyOrderedAggregator
     */
    private $totalQtyOrderedAggregator;
    /**
     * @var State
     */
    private $state;

    /**
     * @param TotalQtyOrderedAggregator $totalQtyOrderedAggregator
     * @param string|null $name
     */
    public function __construct(
        TotalQtyOrderedAggregator $totalQtyOrderedAggregator,
        State $state,
        string $name = null
    ) {
        parent::__construct($name);
        $this->totalQtyOrderedAggregator = $totalQtyOrderedAggregator;
        $this->state = $state;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('product:attribute:aggregate-total-ordered');
        $this->setDescription('Aggregate Total Quantity Ordered for Products.');

        parent::configure();
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');
        $output->writeln('<info>Started Aggregating Total Quantity Ordered for Products...</info>');
        $this->totalQtyOrderedAggregator->insertTotalQtyOrdered();
        $output->writeln('<info>Finished Aggregating Total Quantity Ordered for Products.</info>');
    }
}
