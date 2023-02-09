<?php

namespace FortyFour\Sales\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOrderStatus extends Command
{
    const INPUT_OPTION_STATUS = 'status';
    const INPUT_OPTION_STATE = 'state';
    const INPUT_OPTION_ORDER_INCREMENT_IDS = 'increment_ids';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var State
     */
    private $state;

    /**
     * UpdateOrderStatus constructor.
     * @param LoggerInterface $logger
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderRepository $orderRepository
     * @param State $state
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        OrderCollectionFactory $orderCollectionFactory,
        OrderRepository $orderRepository,
        State $state,
        string $name = null
    ) {
        parent::__construct($name);
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->state = $state;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('sales:order:update-status-and-state');
        $this->setDescription('Update order status and state by increment Ids.');

        $this->addOption(
            self::INPUT_OPTION_STATUS,
            null,
            InputOption::VALUE_REQUIRED,
            'Status'
        );

        $this->addOption(
            self::INPUT_OPTION_STATE,
            null,
            InputOption::VALUE_REQUIRED,
            'State'
        );

        $this->addOption(
            self::INPUT_OPTION_ORDER_INCREMENT_IDS,
            null,
            InputOption::VALUE_REQUIRED,
            'Order Increment Ids'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->validateInputOptions($input)) {
            $output->writeln("<error>Missing the order status, state, or order Ids.</error>");
            return;
        }

        $this->state->setAreaCode(Area::AREA_ADMINHTML);
        $status = $input->getOption(self::INPUT_OPTION_STATUS);
        $state = $input->getOption(self::INPUT_OPTION_STATE);
        $incrementIds = $input->getOption(self::INPUT_OPTION_ORDER_INCREMENT_IDS);
        $incrementIds = explode(',', $incrementIds);
        $orders = $this->getOrdersByIncrementId($incrementIds);

        if (!count($orders)) {
            $output->writeln("<error>Unable to load any orders.</error>");
            return;
        }

        $output->writeln('<info>Started updating order status...</info>');
        foreach($orders as $order) {
            try {
                $this->updateOrderStatusAndState($order, $status, $state);
                $output->writeln("<comment>Updated order {$order->getIncrementId()}.</comment>");
            } catch (\Exception $e) {
                $output->writeln("<error>Unable to update order {$order->getIncrementId()}.</error>");
                $this->logger->error(
                    __('FortyFour\Sales\Console\Command\UpdateOrderStatus::execute() %1', $e->getMessage())
                );
            }
        }
        $output->writeln('<info>Finished updating order statuses.</info>');
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function validateInputOptions(InputInterface $input)
    {
        if (!$input->getOption(self::INPUT_OPTION_STATUS)
            || !$input->getOption(self::INPUT_OPTION_ORDER_INCREMENT_IDS)
            || !$input->getOption(self::INPUT_OPTION_STATE)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $incrementIds
     * @return OrderInterface[]
     */
    private function getOrdersByIncrementId(array $incrementIds)
    {
        return $this->orderCollectionFactory->create()
            ->addFieldToFilter('increment_id', ['in' => $incrementIds])
            ->getItems();
    }

    /**
     * @param OrderInterface $order
     * @param string $status
     * @param string $state
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateOrderStatusAndState(OrderInterface $order, string $status, string $state): void
    {
        $order->setStatus($status)
            ->setState($state);
        $this->orderRepository->save($order);
    }
}
