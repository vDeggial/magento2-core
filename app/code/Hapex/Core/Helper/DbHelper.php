<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class DbHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $resource;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->resource = $this->objectManager->get("Magento\Framework\App\ResourceConnection");
        $this->helperLog = $this->objectManager->get("Hapex\Core\Helper\LogHelper");
    }

    public function getSqlTableName($name = null)
    {
        $tableName = null;
        $tableExists = false;
        try {
            $tableName = $this->resource->getTableName($name);
            $tableExists = $this->resource->getConnection()->isTableExists($tableName);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $tableName = null;
            $tableExists = false;
        } finally {
            return $tableExists ? $tableName : null;
        }
    }

    public function sqlQuery($sql)
    {
        $result = null;
        try {
            $result = $this->resource->getConnection()->query($sql);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            $result = null;
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $result;
        }
    }
}