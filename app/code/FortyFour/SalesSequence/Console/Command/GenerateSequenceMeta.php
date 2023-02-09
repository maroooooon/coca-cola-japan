<?php

namespace FortyFour\SalesSequence\Console\Command;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\SalesSequence\Model\Builder;
use Magento\SalesSequence\Model\Config;
use Magento\SalesSequence\Model\EntityPool;
use Magento\Store\Api\StoreRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSequenceMeta extends Command
{
    const STORES = 'stores';
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var Builder
     */
    private $sequenceBuilder;
    /**
     * @var EntityPool
     */
    private $entityPool;
    /**
     * @var Config
     */
    private $sequenceConfig;

    /**
     * GenerateSequence constructor.
     * @param StoreRepositoryInterface $storeRepository
     * @param Builder $sequenceBuilder
     * @param EntityPool $entityPool
     * @param Config $sequenceConfig
     * @param string|null $name
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        Builder $sequenceBuilder,
        EntityPool $entityPool,
        Config $sequenceConfig,
        string $name = null
    ) {
        parent::__construct($name);
        $this->storeRepository = $storeRepository;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->entityPool = $entityPool;
        $this->sequenceConfig = $sequenceConfig;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('sales:sequence:generate-meta');
        $this->setDescription('Generate the sales sequence for a store or stores.');
        $this->addOption(
            self::STORES,
            null,
            InputOption::VALUE_REQUIRED,
            'Store'
        );

        parent::configure();
    }

    /**
     * Enter store codes as comma separated.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws AlreadyExistsException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Started generating sales sequence meta...</info>');

        $storeCodes = explode(',', $input->getOption(self::STORES));

        if (!$input->getOption(self::STORES) || !count($storeCodes)) {
            $output->writeln('<error>Please enter each store code value separated by a comma.</error>');
        }

        foreach ($storeCodes as $storeCode) {
            if ($store = $this->storeRepository->get($storeCode)) {
                $output->writeln("<info>Generating sequence for '${storeCode}'.</info>");
                $this->generateSequenceMeta($store->getId());
            }
        }

        $output->writeln('<info>Finished generating sales sequence meta.</info>');
    }

    /**
     * @param int $storeId
     * @throws AlreadyExistsException
     */
    private function generateSequenceMeta(int $storeId): void
    {
        foreach ($this->entityPool->getEntities() as $entityType) {
            $this->sequenceBuilder->setPrefix($storeId)
                ->setSuffix($this->sequenceConfig->get('suffix'))
                ->setStartValue($this->sequenceConfig->get('startValue'))
                ->setStoreId($storeId)
                ->setStep($this->sequenceConfig->get('step'))
                ->setWarningValue($this->sequenceConfig->get('warningValue'))
                ->setMaxValue($this->sequenceConfig->get('maxValue'))
                ->setEntityType($entityType)
                ->create();
        }
    }
}
