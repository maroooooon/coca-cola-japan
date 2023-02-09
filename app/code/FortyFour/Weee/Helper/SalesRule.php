<?php

namespace FortyFour\Weee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class SalesRule extends AbstractHelper
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * SalesRule constructor.
     * @param Context $context
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param int $ruleId
     * @return int
     */
    public function getApplyToFpt(int $ruleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            'salesrule',
                'apply_to_fpt'
        )->where('rule_id = ?', $ruleId);

        return (int)$connection->fetchOne($select);
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function canApplyToFpt(\Magento\Quote\Model\Quote $quote): bool
    {
        $ruleIds = explode(',', $quote->getAppliedRuleIds());
        foreach ($ruleIds as $key => $ruleId) {
            if ($this->getApplyToFpt((int)$ruleId)) {
                return true;
            }
        }

        return false;
    }
}
