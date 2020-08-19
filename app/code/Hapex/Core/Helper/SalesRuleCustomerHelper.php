<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class SalesRuleCustomerHelper extends BaseHelper
{
    protected $tableRuleCustomer;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableRuleCustomer = $this->helperDb->getSqlTableName("salesrule_customer");
    }

    public function getRuleTimesUsed($ruleId = 0, $customerId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleCustomerFieldValue($ruleId, $customerId, "times_used");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    private function getRuleCustomerFieldValue($ruleId = 0, $customerId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableRuleCustomer . " WHERE rule_id = $ruleId and customer_id = $customerId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
