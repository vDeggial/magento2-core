<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class SalesRuleHelper extends BaseHelper
{
    protected $salesRuleTableName = null;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->salesRuleTableName = "salesrule";
    }

    protected function getSalesRules($salesRuleId = 0)
    {
        $rule = null;
        try {
            switch (is_array($salesRuleId)) {
                case true:
                    $rule = [];
                    foreach ($salesRuleId as $ruleId) {
                        $rule[] = $this->getSalesRule($ruleId);
                    }
                break;

                default:
                    $rule = $this->getSalesRule($salesRuleId);
                break;
            }
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $rule = null;
        } finally {
            return $rule;
        }
    }

    private function getSalesRule($salesRuleId = 0)
    {
        $rule = null;
        try {
            $tableSalesRule = $this->getSqlTableName($this->salesRuleTableName);
            $sql = "SELECT rule_id, from_date, to_date, is_active FROM $tableSalesRule WHERE rule_id = $salesRuleId";
            $result = $this->sqlQueryFetchRow($sql);
            $rule = [];
            $rule["id"] = (int)$result["rule_id"];
            $rule["active"] = (bool)$result["is_active"];
            $rule["from_date"] = $result["from_date"];
            $rule["to_date"] = $result["to_date"];
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $rule = null;
        } finally {
            return $rule;
        }
    }

    protected function getSalesRuleStatus($rule)
    {
        $isActive = false;
        try {
            $isActive = $rule["active"];
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $isActive = false;
        } finally {
            return $isActive;
        }
    }

    protected function isValidRuleDates($rule)
    {
        $isValid = false;
        try {
            $fromDate = $rule["from_date"];
            $toDate = $rule["to_date"];
            $isValid = $this->isCurrentDateWithinRange($fromDate, $toDate);
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $isValid = false;
        } finally {
            return $isValid;
        }
    }

    protected function setSalesRuleStatus($rule, $status = 0)
    {
        $isSet = false;
        try {
            $ruleId = $rule["id"];
            $tableSalesRule = $this->getSqlTableName($this->salesRuleTableName);
            $sql = "UPDATE $tableSalesRule SET is_active = $status where rule_id = $ruleId";
            $result = $this->sqlQuery($sql);
            $isSet = true;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $isSet = false;
        } finally {
            return $isSet;
        }
    }
}
