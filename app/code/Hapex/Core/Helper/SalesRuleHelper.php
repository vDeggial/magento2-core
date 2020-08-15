<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class SalesRuleHelper extends BaseHelper
{
    protected $tableRule;
    protected $tableRuleCustomer;
    protected $tableRuleCoupon;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableRule = $this->helperDb->getSqlTableName("salesrule");
        $this->tableRuleCustomer = $this->helperDb->getSqlTableName("salesrule_customer");
        $this->tableRuleCoupon = $this->helperDb->getSqlTableName("salesrule_coupon");
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
            $action = $this->getRuleFieldValue($ruleId, "simple_action");
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
            $description = $this->getRuleFieldValue($ruleId, "description");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getRuleDiscountAmount($ruleId = 0)
    {
        $amount = 0;
        try {
            $amount = (int) $this->getRuleFieldValue($ruleId, "discount_amount");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getRuleDiscountQuantity($ruleId = 0)
    {
        $quantity = 0;
        try {
            $quantity = (int) $this->getRuleFieldValue($ruleId, "discount_qty");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $quantity = 0;
        } finally {
            return $quantity;
        }
    }

    public function getRuleDiscountStep($ruleId = 0)
    {
        $step = 0;
        try {
            $step = (int) $this->getRuleFieldValue($ruleId, "discount_step");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $step = 0;
        } finally {
            return $step;
        }
    }

    public function getRuleName($ruleId = 0)
    {
        $name = null;
        try {
            $name = $this->getRuleFieldValue($ruleId, "name");
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
            $date = $this->getRuleFieldValue($ruleId, "from_date");
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
            $date = $this->getRuleFieldValue($ruleId, "to_date");
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

    public function getRuleCouponCode($ruleId = 0)
    {
        $code = null;
        try {
            $code = $this->getRuleCouponFieldValue($ruleId, "code");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $code = null;
        } finally {
            return $code;
        }
    }

    public function getRuleCouponTimesUsed($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleCouponFieldValue($ruleId, "times_used");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
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

    public function setRuleStatus($ruleId = 0, $status = 0)
    {
        return $this->setRuleFieldValue($ruleId, "is_active", $status);
    }

    private function getRuleFieldValue($ruleId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableRule . " WHERE rule_id = $ruleId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }

    private function getRuleCouponFieldValue($ruleId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableRuleCoupon . " WHERE rule_id = $ruleId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
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

    private function setRuleFieldValue($ruleId = 0, $fieldName = null, $value = null)
    {
        $isSet = false;
        try {
            $sql = "UPDATE " . $this->tableRule . " SET $fieldName = $value where rule_id = $ruleId";
            $result = $this->helperDb->sqlQuery($sql);
            $isSet = isset($result);
        } catch (\Exception $e) {
            $this->helperLog->errorLog($e->getMessage());
            $isSet = false;
        } finally {
            return $isSet;
        }
    }
}
