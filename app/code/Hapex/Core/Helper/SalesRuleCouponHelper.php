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
}
