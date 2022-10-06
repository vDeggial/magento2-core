<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class SalesRuleCouponHelper extends BaseHelper
{
    protected $tableRuleCoupon;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableRuleCoupon = $this->helperDb->getSqlTableName("salesrule_coupon");
    }

    public function getRuleCouponCode($ruleId = 0)
    {
        $code = null;
        try {
            $code = $this->getRuleCouponFieldValue($ruleId, "code");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $code = null;
        } finally {
            return $code;
        }
    }

    public function getRuleCouponExpirationDate($ruleId = 0)
    {
        $date = null;
        try {
            $date = $this->getRuleCouponFieldValue($ruleId, "expiration_date");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getRuleCouponIsPrimary($ruleId = 0)
    {
        $isPrimary = 0;
        try {
            $isPrimary = (int) $this->getRuleCouponFieldValue($ruleId, "is_primary");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isPrimary = 0;
        } finally {
            return $isPrimary;
        }
    }

    public function getRuleCouponTimesUsed($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleCouponFieldValue($ruleId, "times_used");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    public function getRuleCouponType($ruleId = 0)
    {
        $type = 0;
        try {
            $type = (int) $this->getRuleCouponFieldValue($ruleId, "type");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $type = 0;
        } finally {
            return $type;
        }
    }

    public function getRuleCouponUsageLimit($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleCouponFieldValue($ruleId, "usage_limit");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    public function getRuleCouponUsagePerCustomer($ruleId = 0)
    {
        $uses = 0;
        try {
            $uses = (int) $this->getRuleCouponFieldValue($ruleId, "usage_per_customer");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $uses = 0;
        } finally {
            return $uses;
        }
    }

    private function getRuleCouponFieldValue($ruleId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableRuleCoupon . " WHERE rule_id = $ruleId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
