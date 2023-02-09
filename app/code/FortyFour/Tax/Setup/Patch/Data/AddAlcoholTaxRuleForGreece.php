<?php

namespace FortyFour\Tax\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Tax\Api\Data\TaxRuleInterfaceFactory;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Psr\Log\LoggerInterface;

class AddAlcoholTaxRuleForGreece implements DataPatchInterface
{
    const ALCOHOLIC_BEVERAGE_TAX_CLASS = 'Alcoholic Beverage - Greece';
    const ALCOHOLIC_BEVERAGE_RULE_GREECE = 'Alcoholic Beverages - Greece';
    const PRODUCT = 'PRODUCT';
    const RETAIL_CUSTOMER = 'Retail Customer';
    const CUSTOMER = 'CUSTOMER';

    /**
     * @var ResourceConnection
     */
    private $resource;
    /**
     * @var AdapterInterface
     */
    private $connection;
    /**
     * @var TaxRuleInterfaceFactory
     */
    private $taxRuleDataObjectFactory;
    /**
     * @var TaxRuleRepositoryInterface
     */
    private $ruleService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SetGenericDeliveryDateMessage constructor.
     * @param ResourceConnection $resource
     * @param TaxRuleInterfaceFactory $taxRuleDataObjectFactory
     * @param TaxRuleRepositoryInterface $ruleService
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resource,
        TaxRuleInterfaceFactory $taxRuleDataObjectFactory,
        TaxRuleRepositoryInterface $ruleService,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->connection = $this->resource->getConnection();
        $this->taxRuleDataObjectFactory = $taxRuleDataObjectFactory;
        $this->ruleService = $ruleService;
        $this->logger = $logger;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [AddAlcoholTaxRateForGreece::class];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this|AddAlcoholTaxRuleForGreece
     * @throws \Magento\Framework\Exception\InputException
     */
    public function apply()
    {
        $this->addAlcoholcProductTaxClass();

        $data = [
            'code' => self::ALCOHOLIC_BEVERAGE_RULE_GREECE,
            'tax_rate' => [$this->getAlcoholicProductTaxRate()],
            'tax_customer_class' => [$this->getRetailCustomerTaxClassId()],
            'tax_product_class' => [$this->getAlcoholicProductsTaxClassId()],
            'priority' => 0,
            'position' => 1
        ];

        $this->logger->debug(print_r($data, true));

        $taxRule = $this->populateTaxRule($data);
        $this->ruleService->save($taxRule);

        return $this;
    }

    /**
     * @return void
     */
    private function addAlcoholcProductTaxClass()
    {
        $data = [
            'class_name' => self::ALCOHOLIC_BEVERAGE_TAX_CLASS,
            'class_type' => self::PRODUCT
        ];
        $this->connection->insert(
            $this->connection->getTableName('tax_class'),
            $data
        );
    }

    /**
     * @return int
     */
    private function getAlcoholicProductsTaxClassId()
    {
        $select = $this->connection->select()->from(
            $this->connection->getTableName('tax_class'),
            'class_id'
        )->where(
            'class_name = ?', self::ALCOHOLIC_BEVERAGE_TAX_CLASS
        )->where(
            'class_type = ?', self::PRODUCT
        );

        return (int)$this->connection->fetchOne($select);
    }

    /**
     * @return int
     */
    private function getRetailCustomerTaxClassId()
    {
        $select = $this->connection->select()->from(
            $this->connection->getTableName('tax_class'),
            'class_id'
        )->where(
            'class_name = ?', self::RETAIL_CUSTOMER
        )->where(
            'class_type = ?', self::CUSTOMER
        );

        return (int)$this->connection->fetchOne($select);
    }

    /**
     * @return int
     */
    private function getAlcoholicProductTaxRate()
    {
        $select = $this->connection->select()->from(
            $this->connection->getTableName('tax_calculation_rate'),
            'tax_calculation_rate_id'
        )->where(
            'code = ?', AddAlcoholTaxRateForGreece::GREECE_ALCOHOL_TAX_CODE
        )->where(
            'tax_country_id = ?', 'GR'
        );

        return (int)$this->connection->fetchOne($select);
    }

    /**
     * @param $postData
     * @return \Magento\Tax\Api\Data\TaxRuleInterface
     */
    private function populateTaxRule($postData)
    {
        $taxRule = $this->taxRuleDataObjectFactory->create();
        if (isset($postData['tax_calculation_rule_id'])) {
            $taxRule->setId($postData['tax_calculation_rule_id']);
        }
        if (isset($postData['code'])) {
            $taxRule->setCode($postData['code']);
        }
        if (isset($postData['tax_rate'])) {
            $taxRule->setTaxRateIds($postData['tax_rate']);
        }
        if (isset($postData['tax_customer_class'])) {
            $taxRule->setCustomerTaxClassIds($postData['tax_customer_class']);
        }
        if (isset($postData['tax_product_class'])) {
            $taxRule->setProductTaxClassIds($postData['tax_product_class']);
        }
        if (isset($postData['priority'])) {
            $taxRule->setPriority($postData['priority']);
        }
        if (isset($postData['calculate_subtotal'])) {
            $taxRule->setCalculateSubtotal($postData['calculate_subtotal']);
        }
        if (isset($postData['position'])) {
            $taxRule->setPosition($postData['position']);
        }
        return $taxRule;
    }
}
