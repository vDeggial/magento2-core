<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class SalesRuleHelper extends BaseHelper
{
    protected $tableSalesRule;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableSalesRule = $this->helperDb->getSqlTableName("salesrule");
    }

    public function ruleExists($ruleId)
    {
        $exists = false;
        try {
            $result = $this->getRuleFieldValue($ruleId, "rule_id");
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    public function getRuleAction($ruleId = 0)
    {
        $action = null;
        try {
            $action = (string) $this->getRuleFieldValue($ruleId, "simple_action");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $action = null;
        } finally {
            return $action;
        }
    }

    public function getRuleDescription($ruleId = 0)
    {
        $description = null;
        try {
            $description = (string) $this->getRuleFieldValue($ruleId, "description");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getRuleName($ruleId = 0)
    {
        $name = null;
        try {
            $name = (string) $this->getRuleFieldValue($ruleId, "name");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getRuleFromDate($ruleId = 0)
    {
        $date = null;
        try {
            $date = (string) $this->getRuleFieldValue($ruleId, "from_date");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getRuleToDate($ruleId = 0)
    {
        $date = null;
        try {
            $date = (string) $this->getRuleFieldValue($ruleId, "to_date");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getRuleStatus($ruleId = 0)
    {
        $status = 0;
        try {
            $status = (int) $this->getRuleFieldValue($ruleId, "is_active");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getRuleUsesPerCustomer($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleFieldValue($ruleId, "uses_per_customer");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    public function isActiveDates($ruleId)
    {
        $isValid = false;
        try {
            $isValid = $this->helperDate->isCurrentDateWithinRange($this->getRuleFromDate($ruleId), $this->getRuleToDate($ruleId));
        } catch (\Exception $e) {
            $this->helperLog->errorLog($e->getMessage());
            $isValid = false;
        } finally {
            return $isValid;
        }
    }

    public function setRuleStatus($ruleId, $status = 0)
    {
        $isSet = false;
        try {
            $sql = "UPDATE " . $this->tableSalesRule . " SET is_active = $status where rule_id = $ruleId";
            $result = $this->helperDb->sqlQuery($sql);
            $isSet = $result !== null;
        } catch (\Exception $e) {
            $this->helperLog->errorLog($e->getMessage());
            $isSet = false;
        } finally {
            return $isSet;
        }
    }

    protected function getRuleFieldValue($ruleId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableSalesRule . " WHERE rule_id = $ruleId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
