<?php

namespace Coke\CompletedOrderQuestionnaire\Console\Command;

use Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Order\CouponReminderSender;
use Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Order\QuestionnaireSender;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class SendReminder extends Command
{
    const REGISTRATION_COUPON_CODE = 'marketing_registration_coupon_code';

    const INPUT_OPTION_SEND_EMAILS_TO = 'send-emails-to';
    const INPUT_OPTION_GET_LIST = 'get-list';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var QuestionnaireSender
     */
    private $reminderSender;
    /**
     * @var OrderCollection
     */
    private $orderCollection;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var State
     */
    private $state;
    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * SendReminder constructor.
     * @param LoggerInterface $logger
     * @param CouponReminderSender $reminderSender
     * @param OrderCollection $orderCollection
     * @param ScopeConfigInterface $scopeConfig
     * @param Emulation $emulation
     * @param State $state
     * @param CollectionFactory $customerCollectionFactory
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        CouponReminderSender $reminderSender,
        OrderCollection $orderCollection,
        ScopeConfigInterface $scopeConfig,
        Emulation $emulation,
        State $state,
        CollectionFactory $customerCollectionFactory,
        QuestionHelper $questionHelper,
        string $name = null
    ) {
        parent::__construct($name);
        $this->logger = $logger;
        $this->reminderSender = $reminderSender;
        $this->orderCollection = $orderCollection;
        $this->scopeConfig = $scopeConfig;
        $this->emulation = $emulation;
        $this->state = $state;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->questionHelper = $questionHelper;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('marketing:questionnaire-reminder');
        $this->setDescription('Send questionnaire reminder');

        $this->addOption(
            self::INPUT_OPTION_SEND_EMAILS_TO,
            null,
            InputOption::VALUE_OPTIONAL,
            'Email List'
        );

        $this->addOption(
            self::INPUT_OPTION_GET_LIST,
            null,
            InputOption::VALUE_OPTIONAL,
            'Get the list'
        );

        parent::configure();
    }

    public function getCustomersWhoHaventUsedCouponQuery()
    {
        $collection = $this->customerCollectionFactory->create();
        $collection->getSelect()
            ->joinInner(
                ['attr_coupon_code' => 'eav_attribute'],
                implode(" and ", [
                    'attr_coupon_code.attribute_code = "' . self::REGISTRATION_COUPON_CODE . '"',
                    'attr_coupon_code.entity_type_id = "' . CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER . '"',
                ]),
                []
            )
            ->joinInner(
                ['c_mrcc' => 'customer_entity_varchar'],
                implode(" and ", [
                    "c_mrcc.attribute_id = attr_coupon_code.attribute_id",
                    "c_mrcc.entity_id = e.entity_id",
                ]),
                []
            )
            ->joinLeft(
                ['o' => 'sales_order'],
                'o.customer_id = e.entity_id AND o.coupon_code = c_mrcc.value',
                []
            )
            ->columns(['c_mrcc.value as coupon_code'])
            ->group('e.entity_id')
            ->having('COUNT(o.entity_id) = 0');

        return $collection;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_ADMINHTML);

        if ($input->getOption(self::INPUT_OPTION_GET_LIST)) {
            $query = $this->getCustomersWhoHaventUsedCouponQuery();
            $data = $query->getData();

            $output->writeln('');
            foreach ($data as $row) {
                $output->writeln($row['email']);
            }

            $output->writeln('');
            return 0;
        }

        if (($list = $input->getOption(self::INPUT_OPTION_SEND_EMAILS_TO))) {
            $content = file_get_contents($list);
            if ($content === false) {
                $output->writeln('Couldnt read file ' . $list);
                return 1;
            }

            $emails = explode("\n", $content);
            $emails = array_filter($emails);
            $count = count($emails);

            $answer = $this->questionHelper->ask($input, $output, new Question('Send email to ' . $count . ' emails? [y/N] '));
            if (strtolower($answer) !== 'y') {
                $output->writeln('Aborted');
                return 1;
            }

            foreach ($emails as $email) {
                $this->emulation->startEnvironmentEmulation(49, Area::AREA_FRONTEND, true);

                $output->writeln(sprintf('Sending coupon reminder email. email = %s', $email));
                $this->reminderSender->send([], $email);
                $this->logger->info(sprintf('Sent coupon reminder email. email = %s', $email));

                $this->emulation->stopEnvironmentEmulation();
            }

            return 0;
        }

        $output->writeln('Did nothing');
    }
}
