<?php

namespace Coke\Logicbroker\Console;

use Magento\Framework\App\Area;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Coke\Logicbroker\Cron\LogicbrokerPullCancellationsFactory;

class PullCancellationsCommand extends Command
{
	/**
	 * @var LogicbrokerPullCancellationsFactory
	 */
	private $pullCancellationsFactory;

	/**
	 * @var State
	 */
	private $state;

	/**
	 * CartCommand constructor.
	 * @param State $state
	 * @param LogicbrokerPullCancellationsFactory $pullCancellations
	 */
	public function __construct(
		State $state,
		LogicbrokerPullCancellationsFactory $pullCancellations
	) {
		$this->state = $state;
		$this->pullCancellationsFactory = $pullCancellations;
		parent::__construct();
	}

	/**
	 * @inheritDoc
	 */
	protected function configure()
	{
		$this->setName('lb:cancellations');
		$this->setDescription('Pulls cancelled orders from Logicbroker');

		parent::configure();
	}

	/**
	 * Send order to logicbroker
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	public function pullCancellations(InputInterface $input, OutputInterface $output)
	{
		$this->pullCancellationsFactory->create()->execute();
	}

	/**
	 * @inheritDoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$this->state->emulateAreaCode(
				Area::AREA_FRONTEND,
				[$this, 'pullCancellations'],
				[$input, $output]
			);
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
		}
	}
}
