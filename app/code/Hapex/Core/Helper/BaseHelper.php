<?php
namespace Hapex\Core\Helper;

use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class BaseHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    public function getRootPath()
    {
        $directory = $this->generateClassObject("Magento\Framework\Filesystem\DirectoryList");
        return $directory->getRoot();
    }

    public function getFileContents($path = "", $filename = "")
    {
        return file_get_contents($this->getRootPath() . "/$path/$filename");
    }

    public function printLog($filename = null, $message = null)
    {
        return $this->writeLogEntry("info", $filename, $message);
    }

    public function sendOutput($output)
    {
        try {
            print_r($output);
            return true;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            return false;
        }
    }

    protected function errorLog($message = null)
    {
        return $this->writeLogEntry("error", "hapex_error_log", $message);
    }

    protected function generateClassObject($class = "")
    {
        $object = null;
        try {
            $object = $this->objectManager->get($class);
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $object = null;
        } finally {
            return $object;
        }
    }

    protected function getCurrentDate()
    {
        $date = null;
        try {
            $timezone = $this->generateClassObject("Magento\Framework\Stdlib\DateTime\TimezoneInterface");
            $date = $timezone->date();
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    protected function getSqlTableName($name = null)
    {
        $tableName = null;
        $tableExists = false;
        try {
            $resource = $this->getSqlResource();
            $tableName = $resource->getTableName($name);
            $tableExists = $resource->getConnection()->isTableExists($tableName);
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $tableName = null;
            $tableExists = false;
        } finally {
            return $tableExists ? $tableName : null;
        }
    }

    public function isCurrentDateWithinRange($fromDate, $toDate)
    {
        $isWithinRange = false;
        $afterFromDate = false;
        $beforeToDate = false;
        $currentDate = null;

        try {
            $currentDate = $this->getCurrentDate()->format('Y-m-d');
            $afterFromDate = $fromDate ? strtotime($currentDate) >= strtotime($fromDate) ? true : false : true;
            $beforeToDate = $toDate ? strtotime($currentDate) <= strtotime($toDate) ? true : false : true;
            $isWithinRange = $afterFromDate && $beforeToDate;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $isWithinRange = false;
        } finally {
            return $isWithinRange;
        }
    }

    protected function sortDataByColumn(&$data = [], $sortColumn = "qty", $sortDirection = SORT_DESC)
    {
        array_multisort(array_column($data, $sortColumn), $sortDirection, $data);
    }

    protected function sqlQuery($sql)
    {
        return $this->queryExecute($sql);
    }

    protected function sqlQueryFetchAll($sql, $limit = 0)
    {
        $sql .= ($limit > 0) ? " LIMIT $limit" : "";
        return $this->queryExecute($sql, "fetchAll");
    }

    protected function sqlQueryFetchOne($sql)
    {
        return $this->queryExecute($sql, "fetchOne");
    }

    protected function sqlQueryFetchRow($sql)
    {
        return $this->queryExecute($sql, "fetchRow");
    }

    protected function urlExists($remoteUrl = "")
    {
        $exists = false;
        try {
            $exists = strpos(@get_headers($remoteUrl) [0], '404') === false;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    private function getSqlResource()
    {
        return $this->generateClassObject("Magento\Framework\App\ResourceConnection");
    }

    private function queryExecute($sql = null, $command = null)
    {
        $result = null;
        try {
            $resource = $this->getSqlResource();
            $connection = $resource->getConnection();

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
            $this->errorLog($e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }

    private function writeLogEntry($type = "info", $filename = null, $message = null)
    {
        try {
            $writer = new Stream(BP . "/var/log/$filename.log");
            $logger = new Logger();
            $formatter = new Formatter\Simple();
            $formatter->setDateTimeFormat("Y-m-d H:i:s T");
            $writer->setFormatter($formatter);
            $logger->addWriter($writer);

            switch ($type) {
                case "info":
                    $logger->info($message);
                    break;
                case "error":
                    $logger->error($message);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
