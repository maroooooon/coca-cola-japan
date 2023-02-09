<?php

namespace Coke\CompletedOrderQuestionnaire\Console\Command;

use Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Order\TocAlertSender;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class SendTocAlert extends Command
{
    const INPUT_OPTION_SEND_EMAILS_TO = 'send-emails-to';
    const INPUT_OPTION_SEND_ALL = 'send-emails-all';
    const INPUT_OPTION_STORE_ID = 'store-id';

    /**
     * @var QuestionHelper
     */
    private $questionHelper;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var TocAlertSender
     */
    private $tocAlertSender;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var State
     */
    private $state;
    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * send TOC alert constructor.
     * @param LoggerInterface $logger ,
     * @param TocAlertSender $tocAlertSender
     * @param Emulation $emulation ,
     * @param customerCollectionFactory $customerFactory,
     * @param QuestionHelper $questionHelper ,
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        TocAlertSender $tocAlertSender,
        Emulation $emulation,
        CollectionFactory $customerCollectionFactory,
        QuestionHelper $questionHelper,
        State $state,
        string $name = null
    ) {
        parent::__construct($name);
        $this->logger = $logger;
        $this->tocAlertSender = $tocAlertSender;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->questionHelper = $questionHelper;
        $this->emulation = $emulation;
        $this->state = $state;
        $this->name = $name;
    }

    protected function configure()
    {
        $this->setName('marketing:send-toc-change');
        $this->setDescription('Send TOC change email to customers');

        $this->addOption(
            self::INPUT_OPTION_SEND_EMAILS_TO,
            null,
            InputOption::VALUE_REQUIRED,
            'Single Email'
        );

        $this->addOption(
            self::INPUT_OPTION_SEND_ALL,
            null,
            InputOption::VALUE_NONE,
            'Send email to all'
        );

        $this->addOption(
            self::INPUT_OPTION_STORE_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Send Store ID'
        );

        parent::configure();
    }

    /**
     * Get customer collection to send TOC email
     * @return array
     */
    public function getCustomersToSendEmail($storeId)
    {
        $customerArray = [];
        $collection = $this->customerCollectionFactory->create()->addAttributeToFilter('store_id',$storeId);
        foreach ($collection as $customer) {
            $email = $customer->getEmail();
            if (!str_contains($email, 'disabled_')) {
                $customerArray[] = $email;
            }
        }
        return $customerArray;
    }

    public function checkIfCustomerExists($email,$storeId){
        $customerCollection = $this->customerCollectionFactory->create()
            ->addAttributeToFilter("email",$email)
            ->addAttributeToFilter('store_id',$storeId)
            ->load();
        if((!empty($customerCollection->getData())) && (array_key_exists('0',$customerCollection->getData()))){
            return 1;
        }else{
            return 0;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_ADMINHTML);

        if($input->getOption(self::INPUT_OPTION_STORE_ID)){
            $storeId = $input->getOption(self::INPUT_OPTION_STORE_ID);
            if(($input->getOption(self::INPUT_OPTION_STORE_ID)) && ($input->getOption(self::INPUT_OPTION_SEND_EMAILS_TO))) {
                $customerEmail = $input->getOption(self::INPUT_OPTION_SEND_EMAILS_TO);
                $customerCheck = $this->checkIfCustomerExists($customerEmail, $storeId);
                if ($customerCheck) {
                    $answer = $this->questionHelper->ask($input, $output, new Question('Send email to ' . $customerEmail . '? [y/N] '));
                    if (strtolower($answer) !== 'y') {
                        $output->writeln('Aborted');
                        return 1;
                    }

                    $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
                    $output->writeln(sprintf('Sending TOC update email. email = %s', $customerEmail));
                    $this->tocAlertSender->send([], $customerEmail);
                    $output->writeln(sprintf('Sent TOC update email. email = %s', $customerEmail));
                    $this->logger->info(sprintf('Sent TOC update email. email = %s', $customerEmail));
                    $this->emulation->stopEnvironmentEmulation();
                    return 0;
                }else{
                    $output->writeln("Customer Does not exists.");
                    return 1;
                }
            }

            if(($input->getOption(self::INPUT_OPTION_STORE_ID)) && ($input->getOption(self::INPUT_OPTION_SEND_ALL))) {
                $customerEmails = $this->getCustomersToSendEmail($storeId);
                $emailCount = count($customerEmails);

                $answer = $this->questionHelper->ask($input, $output, new Question('Send email to ' . $emailCount . ' emails? [y/N] '));
                if (strtolower($answer) !== 'y') {
                    $output->writeln('Aborted');
                    return 1;
                }

                foreach ($customerEmails as $email) {
                    $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
                    $output->writeln(sprintf('Sending TOC update email. email = %s', $email));
                    $this->tocAlertSender->send([], $email);
                    $output->writeln(sprintf('Sent TOC update email. email = %s', $email));
                    $this->logger->info(sprintf('Sent TOC update email. email = %s', $email));
                    $this->emulation->stopEnvironmentEmulation();
                }
                return 0;
            }
        }else{
            $output->writeln('Please specify the store ID using --store-id=');
        }
    }
}
