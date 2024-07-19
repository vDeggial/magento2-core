<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class MpRewardsHelper extends BaseHelper
{
    protected $tableRewardTransactions;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableRewardTransactions = $this->helperDb->getSqlTableName("mageplaza_reward_transaction");
    }

    public function getMpRewards($select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions;
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardById($transactionId = 0, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE transaction_id = $transactionId";
            $result = $this->helperDb->sqlQueryFetchRow($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardByCustomerId($customerId = 0, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE customer_id = $customerId";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardsByActionType($actionType = 0, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE action_type = $actionType";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardsByActionCode($actionCode = null, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE action_code LIKE '$actionCode'";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardsByStatus($status = 0, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE status = $status";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardsByDateCreated($dateCreated = null, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE created_at LIKE '%$dateCreated%'";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getMpRewardsByExtraContent($extraContent = null, $select = "*")
    {
        $result = null;
        try {
            $sql = "SELECT $select FROM " . $this->tableRewardTransactions . " WHERE extra_content LIKE '%$extraContent%'";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }
}
