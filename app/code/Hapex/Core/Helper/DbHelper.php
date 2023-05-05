<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class DbHelper extends AbstractHelper
{
    protected $resource;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, ResourceConnection $resource, LogHelper $helperLog)
    {
        parent::__construct($context, $objectManager);
        $this->resource = $resource;
        $this->helperLog = $helperLog;
    }

    public function getSqlTableName($name = null)
    {
        $tableName = null;
        $tableExists = false;
        try {
            $tableName = isset($name) ? $this->resource->getTableName($name) : $name;
            $tableExists = isset($tableName) ? $this->resource->getConnection()->isTableExists($tableName) : isset($name);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $tableName = $name;
            $tableExists = false;
        } finally {
            return $tableExists ? $tableName : $name;
        }
    }

    public function sqlQuery($sql)
    {
        $result = null;
        try {
            $result = $this->resource->getConnection()->query($sql);
        } catch (\Throwable $e) {
            $result = null;
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $result;
        }
    }

    public function sqlQueryFetchAll($sql, $limit = 0)
    {
        $sql .= ($limit > 0) ? " LIMIT $limit" : "";
        $result = null;
        try {
            $result = $this->resource->getConnection()->fetchAll($sql);
        } catch (\Throwable $e) {
            $result = null;
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $result;
        }
    }

    public function sqlQueryFetchOne($sql)
    {
        $result = null;
        try {
            $result = $this->resource->getConnection()->fetchOne($sql);
        } catch (\Throwable $e) {
            $result = null;
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $result;
        }
    }

    public function sqlQueryFetchRow($sql)
    {
        $result = null;
        try {
            $result = $this->resource->getConnection()->fetchRow($sql);
        } catch (\Throwable $e) {
            $result = null;
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $result;
        }
    }
}
