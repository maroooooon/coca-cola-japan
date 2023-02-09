<?php

namespace Coke\Sarp2\Console;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Aheadworks\Sarp2\Model\Profile\Source\Status;
use Coke\Sarp2\Api\ProfileManagementInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\State;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePayments extends Command
{
    const PROFILE_IDS = 'profile-ids';
    const STORE_ID = 'store-id';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProfileManagementInterface
     */
    private $profileManagement;
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var State
     */
    private $state;

    /**
     * @param LoggerInterface $logger
     * @param ProfileManagementInterface $profileManagement
     * @param ProfileRepositoryInterface $profileRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Emulation $emulation
     * @param State $state
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        ProfileManagementInterface $profileManagement,
        ProfileRepositoryInterface $profileRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Emulation $emulation,
        State $state,
        string $name = null
    ) {
        parent::__construct($name);
        $this->logger = $logger;
        $this->profileManagement = $profileManagement;
        $this->profileRepository = $profileRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->emulation = $emulation;
        $this->state = $state;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('sarp2:profile:update-payments');
        $this->setDescription('Update Aheadworks_Sarp2 profile payments.');
        $this->addOption(
            self::PROFILE_IDS,
            null,
            InputOption::VALUE_OPTIONAL,
            'Comma-separated profile ids.'
        );

        $this->addOption(
            self::STORE_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Single Store ID'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');

        if (!($storeId = $input->getOption(self::STORE_ID))) {
            $output->writeln('<error>Please enter a store id. Example: --store-id=49</error>');
        }

        if ($profiles = $this->getProfiles($storeId, $input->getOption(self::PROFILE_IDS))) {
            $this->emulation->startEnvironmentEmulation($storeId);
            $output->writeln('<comment>Started updating subscription profile payments...</comment>');

            foreach ($profiles as $profile) {
                try {
                    if ($this->profileManagement->updatePayments($profile->getProfileId())) {
                        $output->writeln(
                            __('<info>Updated payments for profile: %1.</info>', $profile->getProfileId())
                        );
                    }
                } catch (CouldNotDeleteException | CouldNotSaveException | NoSuchEntityException $e) {
                    $output->writeln(
                        __('<error>Unable to update payments for profile: %1.</error>', $profile->getProfileId())
                    );
                    $this->logger->info(__('[UpdatePayments] Error: %1', $e->getMessage()));
                }
            }

            $output->writeln('<comment>Done updating subscription profile payments.</comment>');
            $this->emulation->stopEnvironmentEmulation();
            return;
        }

        $output->writeln('<info>Nothing to update.</info>');
    }

    /**
     * @param $storeId
     * @param null $profileIds
     * @return array|null
     */
    private function getProfiles($storeId, $profileIds = null): ?array
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(
                ProfileInterface::STATUS,
                Status::ACTIVE
            )->addFilter(
                ProfileInterface::STORE_ID,
                $storeId
            );

            if ($profileIds) {
                $searchCriteria->addFilter(
                    ProfileInterface::PROFILE_ID,
                    explode(',', $profileIds),
                    'in'
                );
            }

            return $this->profileRepository->getList($searchCriteria->create())->getItems();
        } catch (LocalizedException $e) {
            return null;
        }
    }
}
