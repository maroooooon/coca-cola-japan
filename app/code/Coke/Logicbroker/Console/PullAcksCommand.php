<?php

namespace Coke\Logicbroker\Console;

use Magento\Framework\App\Area;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use \Logicbroker\RetailerAPI\Jobs\Cron\PullAcks;

class PullAcksCommand extends Command
{
	private State $state;
	private PullAcks $pullAcks;
	/**
	 * PullShipmentsCommand constructor.
	 * @param State $state
	 * @param PullAcks $pullAcks
	 */
	public function __construct(
		State $state,
		PullAcks $pullAcks
	) {
		$this->state = $state;
		$this->pullAcks = $pullAcks;
		parent::__construct();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function configure()
	{
		$this->setName('lb:acks');
		$this->setDescription('Pull acknowledgments from Logicbroker');
		
		parent::configure();
	}
	
	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	public function pullAcks(InputInterface $input, OutputInterface $output)
	{
		$this->pullAcks->execute();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$this->state->emulateAreaCode(
				Area::AREA_FRONTEND,
				[$this, 'pullAcks'],
				[$input, $output]
			);
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
		}
	}
}
