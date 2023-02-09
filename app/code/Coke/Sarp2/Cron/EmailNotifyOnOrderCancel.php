<?php

namespace Coke\Sarp2\Cron;

use Aheadworks\Sarp2\Model\ResourceModel\Profile\CollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Sarp2\Model\Config;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

class EmailNotifyOnOrderCancel
{
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REATTEMPT = 'reattempt';
    /**
     * @var CollectionFactory
     */
    private $profileCollectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @param ResourceConnection $resource
     * @param CollectionFactory $profileCollectionFactory
     * @param Config $config
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param ProfileRepositoryInterface $profileRepository
     * @param LoggerInterface $logger
     * @param Emulation $emulation
     */

    public function __construct(
        CollectionFactory $profileCollectionFactory,
        ResourceConnection $resource,
        Config $config,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ProfileRepositoryInterface $profileRepository,
        LoggerInterface $logger,
        Emulation $emulation
    ) {
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->resource = $resource;
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->profileRepository = $profileRepository;
        $this->logger = $logger;
        $this->emulation = $emulation;
    }
    public function execute(): void
    {
        $this->logger->info('Running Subscription Cancel Notification Email cron...');
        $maxRetriesCount = $this->config->getMaxRetriesCount();
        $connection  = $this->resource->getConnection();
        $profileCollection = $this->profileCollectionFactory->create()
            ->addFieldToSelect('customer_email')
            ->addFieldToSelect('customer_fullname')
            ->addFieldToSelect('status')
            ->addFieldToSelect('is_email_sent_on_cancellation');
        $profileCollection->getSelect()->join(
            ['schedule_table'=> $connection->getTableName('aw_sarp2_core_schedule')],
            'main_table.profile_id = schedule_table.schedule_id');
        $profileCollection->getSelect()
            ->join(
                ['schedule_item_table' => $connection->getTableName('aw_sarp2_core_schedule_item')],
                'schedule_table.schedule_id = schedule_item_table.schedule_id'
                . ' AND ' . ' schedule_item_table.schedule_id IS NOT NULL'
                . ' AND ' . ' schedule_item_table.type = "' . self::STATUS_REATTEMPT .'"'
                . ' AND ' . ' schedule_item_table.retries_count = ' . $maxRetriesCount
                . ' AND ' . ' schedule_item_table.payment_status = "' . self::STATUS_FAILED .'"'
                . ' AND ' . ' main_table.status = "' . self::STATUS_CANCELLED .'"'
                . ' AND ' . ' main_table.is_email_sent_on_cancellation = "0"'
            );
        if(!empty($profileCollection->getData())){
            foreach($profileCollection as $profileData){
                $store = $profileData->getStoreId();
                $this->emulation->startEnvironmentEmulation($store, Area::AREA_FRONTEND, true);
                $profileId = $profileData->getProfileId();
                $name = $profileData->getData('customer_fullname');
                $email = $profileData->getCustomerEmail();
                $this->logger->info(sprintf('Sending Subscription cancel email. email = %s', $email));
                $this->dispatchEmail($name,$email);
                $profile = $this->profileRepository->get($profileId);
                $profile->setIsEmailSentOnCancellation("1");
                $this->profileRepository->save($profile);
                $this->emulation->stopEnvironmentEmulation();
            }
        }
    }

    /**
     * Send email to the customer whose subscription is cancelled
     *
     * @return $this
     */
    private function dispatchEmail($name,$email)
    {
        $store = $this->storeManager->getStore()->getId();
        $data = [
            'customerName' => $name
        ];
        $transport = $this->transportBuilder->setTemplateIdentifier('subscription_cancelled_email')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars($data)
            ->setFrom($this->config->getSenderData($store))
            ->addTo($email, $name)
            ->getTransport();
        $transport->sendMessage();
        return $this;
    }
}
