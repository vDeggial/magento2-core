<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class SalesRuleHelper extends BaseHelper
{
    protected $tableSalesRule;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableSalesRule = $this->getSqlTableName("salesrule");
    }

    public function ruleExists($ruleId)
    {
        $exists = false;
        try {
            $sql = "SELECT rule_id FROM " . $this->tableSalesRule . " WHERE rule_id = $ruleId";
            $result = $this->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    public function getRuleFromDate($ruleId = 0)
    {
        $date = null;
        try {
            $sql = "SELECT from_date FROM " . $this->tableSalesRule . " WHERE rule_id = $ruleId";
            $result = $this->sqlQueryFetchOne($sql);
            $date = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getRuleToDate($ruleId = 0)
    {
        $date = null;
        try {
            $sql = "SELECT to_date FROM " . $this->tableSalesRule . " WHERE rule_id = $ruleId";
            $result = $this->sqlQueryFetchOne($sql);
            $date = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getRuleStatus($ruleId = 0)
    {
        $status = 0;
        try {
            $sql = "SELECT is_active FROM " . $this->tableSalesRule . " WHERE rule_id = $ruleId";
            $result = $this->sqlQueryFetchOne($sql);
            $status = (int)$result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function isActiveDates($ruleId)
    {
        $isValid = false;
        try {
            $isValid = $this->isCurrentDateWithinRange($this->getRuleFromDate($ruleId), $this->getRuleToDate($ruleId));
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
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
            $result = $this->sqlQuery($sql);
            $isSet = $result !== null;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $isSet = false;
        } finally {
            return $isSet;
        }
    }
}
