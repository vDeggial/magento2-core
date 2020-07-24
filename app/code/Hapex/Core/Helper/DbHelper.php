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
        return $this->queryExecute($sql);
    }

    public function sqlQueryFetchAll($sql, $limit = 0)
    {
        $sql .= ($limit > 0) ? " LIMIT $limit" : "";
        return $this->queryExecute($sql, "fetchAll");
    }

    public function sqlQueryFetchOne($sql)
    {
        return $this->queryExecute($sql, "fetchOne");
    }

    public function sqlQueryFetchRow($sql)
    {
        return $this->queryExecute($sql, "fetchRow");
    }

    private function queryExecute($sql = null, $command = null)
    {
        $result = null;
        try {
            $connection = $this->resource->getConnection();

            switch ($command) {
                case "fetchOne":
                    $result = $connection->fetchOne($sql);
                break;

                case "fetchAll":
                    $result = $connection->fetchAll($sql);
                break;

                case "fetchRow":
                    $result = $connection->fetchRow($sql);
                break;

                default:
                    $result = $connection->query($sql);
                break;
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
