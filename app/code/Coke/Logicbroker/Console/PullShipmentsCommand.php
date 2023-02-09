<?php

namespace Coke\Logicbroker\Console;

use Coke\Logicbroker\Cron\LogicbrokerPullShipmentsFakeable;
use Logicbroker\RetailerAPI\Jobs\Cron\PullShipments;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;

class PullShipmentsCommand extends Command
{
    /**
     * @var State
     */
    private $state;

    /**
     * PullShipmentsCommand constructor.
     * @param State $state
     */
    public function __construct(
        State $state
    ) {
        $this->state = $state;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('lb:shipments');
        $this->setDescription('Pull shipments from Logicbroker');
        $this->addOption(
            'file',
            'f',
            InputOption::VALUE_OPTIONAL
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function pullShipments(InputInterface $input, OutputInterface $output)
    {
        if (($file = $input->getOption('file'))) {
            $pullShipments = ObjectManager::getInstance()
                ->create(LogicbrokerPullShipmentsFakeable::class, [
                    'shipmentFiles' => [$file]
                ]);
        } else {
            $pullShipments = ObjectManager::getInstance()->get(PullShipments::class);
        }

        $pullShipments->execute();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->emulateAreaCode(
                Area::AREA_FRONTEND,
                [$this, 'pullShipments'],
                [$input, $output]
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
