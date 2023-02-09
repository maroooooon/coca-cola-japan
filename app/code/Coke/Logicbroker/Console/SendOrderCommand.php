<?php

namespace Coke\Logicbroker\Console;

use Magento\Framework\App\Area;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Logicbroker\RetailerAPI\Jobs\Cron\SendOrders;

class SendOrderCommand extends Command
{
    /**
     * @var SendOrders
     */
    private $sendOrders;

    /**
     * @var State
     */
    private $state;

    /**
     * CartCommand constructor.
     * @param State $state
     * @param SendOrders $sendOrders
     */
    public function __construct(
        State $state,
        SendOrders $sendOrders
    ) {
        $this->state = $state;
        $this->sendOrders = $sendOrders;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('lb:order');
        $this->setDescription('Send order to Logicbroker');

        parent::configure();
    }

    /**
     * Send order to logicbroker
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function sendOrders(InputInterface $input, OutputInterface $output)
    {
        $this->sendOrders->execute();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->emulateAreaCode(
                Area::AREA_FRONTEND,
                [$this, 'sendOrders'],
                [$input, $output]
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
