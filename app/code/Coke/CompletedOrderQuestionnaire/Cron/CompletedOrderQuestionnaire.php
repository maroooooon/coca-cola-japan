<?php

namespace Coke\CompletedOrderQuestionnaire\Cron;

use Coke\CompletedOrderQuestionnaire\Model\Email\Sender\Order\QuestionnaireSender;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class CompletedOrderQuestionnaire
{
    const XML_QUESTIONNAIRE_SEND_AFTER_DAYS = 'sales_email/completed_order_questionnaire/questionnaire_send_after_days';
    const QUESTIONNAIRE_SENT = 'questionnaire_sent';
    const REGISTRATION_COUPON_CODE = 'marketing_registration_coupon_code';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var QuestionnaireSender
     */
    private $questionnaireSender;
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
     * @var Attribute
     */
    private $eavAttribute;
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * CompletedOrderQuestionnaire constructor.
     * @param LoggerInterface $logger
     * @param QuestionnaireSender $questionnaireSender
     * @param OrderCollection $orderCollection
     * @param ScopeConfigInterface $scopeConfig
     * @param Emulation $emulation
     */
    public function __construct(
        LoggerInterface $logger,
        QuestionnaireSender $questionnaireSender,
        OrderCollection $orderCollection,
        ScopeConfigInterface $scopeConfig,
        Emulation $emulation,
        State $state,
        Attribute $eavAttribute,
        ResourceConnection $resource
    ){
        $this->logger = $logger;
        $this->questionnaireSender = $questionnaireSender;
        $this->orderCollection = $orderCollection;
        $this->scopeConfig = $scopeConfig;
        $this->emulation = $emulation;
        $this->state = $state;
        $this->eavAttribute = $eavAttribute;
        $this->resource = $resource;
    }

    public function execute(): void
    {
        $this->logger->info('Running CompletedOrderQuestionnaire cron...');

        $days = (int)$this->scopeConfig->getValue(self::XML_QUESTIONNAIRE_SEND_AFTER_DAYS);
        if (!$days) {
            return;
        }

        $questionnaireSentAttributeId = $this->eavAttribute->getIdByCode(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            self::QUESTIONNAIRE_SENT
        );

        $orderCollection = $this->orderCollection->create();
        $orderCollection->addFieldToSelect(['store_id', 'customer_id', 'customer_email', 'customer_firstname', 'customer_lastname', 'increment_id']);
        $orderCollection->getSelect()
            ->joinLeft(
                ['c' => 'customer_entity'],
                'main_table.customer_email = c.email',
                []
            )
            ->joinLeft(
                ['attr_coupon_code' => 'eav_attribute'],
                implode(" and ", [
                    'attr_coupon_code.attribute_code = "' . self::REGISTRATION_COUPON_CODE . '"',
                    'attr_coupon_code.entity_type_id = "' . CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER . '"',
                ]),
                []
            )
            ->joinLeft(
                ['c_qs' => 'customer_entity_int'],
                implode(" and ", [
                    "c_qs.attribute_id = " . $questionnaireSentAttributeId,
                    "c_qs.entity_id = c.entity_id",
                ]),
                []
            )
            ->joinInner(
                ['c_mrcc' => 'customer_entity_varchar'],
                implode(" and ", [
                    "c_mrcc.attribute_id = attr_coupon_code.attribute_id",
                    "c_mrcc.entity_id = c.entity_id",
                ]),
                []
            )
            ->joinInner(
                ['o' => 'sales_order'],
                'o.customer_id = c.entity_id AND o.coupon_code = c_mrcc.value',
                []
            )
            ->where('c_qs.value = 0 OR c_qs.value is null')
            ->where('main_table.created_at <= DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)');

        foreach ($orderCollection->getData() as $order) {
            $this->emulation->startEnvironmentEmulation($order['store_id'], Area::AREA_FRONTEND, true);

            $customerEmail = $order['customer_email'];
            $params = [
                'order_id' => $order['increment_id'],
            ];

            $this->logger->info(sprintf('Sending questionnaire. order = %s, email = %s', $params['order_id'], $customerEmail));
            $this->questionnaireSender->send($params, $customerEmail);

            // update attribute
            $this->resource->getConnection()
                ->insertOnDuplicate(
                    'customer_entity_int',
                    ['entity_id' => $order['customer_id'], 'attribute_id' => $questionnaireSentAttributeId, 'value' => 1],
                    ['value'],
                );

            $this->emulation->stopEnvironmentEmulation();
        }
    }
}
