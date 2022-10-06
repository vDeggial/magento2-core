<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class SalesRuleHelper extends BaseHelper
{
    protected $tableRule;
    protected $helperRuleCoupon;
    protected $helperRuleCustomer;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        SalesRuleCouponHelper $helperRuleCoupon,
        SalesRuleCustomerHelper $helperRuleCustomer
    ) {
        parent::__construct($context, $objectManager);
        $this->helperRuleCoupon = $helperRuleCoupon;
        $this->helperRuleCustomer = $helperRuleCustomer;
        $this->tableRule = $this->helperDb->getSqlTableName("salesrule");
    }

    public function ruleExists($ruleId = 0)
    {
        $exists = false;
        try {
            $result = $this->getRuleFieldValue($ruleId, "rule_id");
            $exists = $result && !empty($result);
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $action = null;
        } finally {
            return $action;
        }
    }

    public function getRuleApplyToShipping($ruleId = 0)
    {
        $applyToShipping = 0;
        try {
            $applyToShipping = (int) $this->getRuleFieldValue($ruleId, "apply_to_shipping");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $applyToShipping = 0;
        } finally {
            return $applyToShipping;
        }
    }

    public function getRuleCouponType($ruleId = 0)
    {
        $type = 0;
        try {
            $type = (int) $this->getRuleFieldValue($ruleId, "coupon_type");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $type = 0;
        } finally {
            return $type;
        }
    }

    public function getRuleDescription($ruleId = 0)
    {
        $description = null;
        try {
            $description = $this->getRuleFieldValue($ruleId, "description");
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getRuleIsAdvanced($ruleId = 0)
    {
        $isAdvanced = 0;
        try {
            $isAdvanced = (int) $this->getRuleFieldValue($ruleId, "is_advanced");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isAdvanced = 0;
        } finally {
            return $isAdvanced;
        }
    }

    public function getRuleIsRss($ruleId = 0)
    {
        $isRss = 0;
        try {
            $isRss = (int) $this->getRuleFieldValue($ruleId, "is_rss");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isRss = 0;
        } finally {
            return $isRss;
        }
    }

    public function getRuleSortOrder($ruleId = 0)
    {
        $order = 0;
        try {
            $order = (int) $this->getRuleFieldValue($ruleId, "sort_order");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $order = 0;
        } finally {
            return $order;
        }
    }

    public function getRuleStatus($ruleId = 0)
    {
        $status = 0;
        try {
            $status = (int) $this->getRuleFieldValue($ruleId, "is_active");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getRuleStopProcessing($ruleId = 0)
    {
        $stopProcessing = 0;
        try {
            $stopProcessing = (int) $this->getRuleFieldValue($ruleId, "stop_rules_processing");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $stopProcessing = 0;
        } finally {
            return $stopProcessing;
        }
    }

    public function getRuleTimesUsed($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleFieldValue($ruleId, "times_used");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    public function getRuleUsesPerCoupon($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleFieldValue($ruleId, "uses_per_coupon");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    public function getRuleUsesPerCustomer($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleFieldValue($ruleId, "uses_per_customer");
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog($e->getMessage());
            $isSet = false;
        } finally {
            return $isSet;
        }
    }
}
