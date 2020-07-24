<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class BaseHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $helperDb;
    protected $helperLog;
    protected $helperFile;
    protected $helperDate;
    protected $helperUrl;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->helperDb = $this->generateClassObject("Hapex\Core\Helper\DbHelper");
        $this->helperLog = $this->generateClassObject("Hapex\Core\Helper\LogHelper");
        $this->helperFile = $this->generateClassObject("Hapex\Core\Helper\FileHelper");
        $this->helperDate = $this->generateClassObject("Hapex\Core\Helper\DateHelper");
        $this->helperUrl = $this->generateClassObject("Hapex\Core\Helper\UrlHelper");
        parent::__construct($context);
    }

    public function getFileContents($path = "", $filename = "")
    {
        return $this->helperFile->getFileContents($path, $filename);
    }

    public function printLog($filename = null, $message = null)
    {
        return $this->helperLog->printLog($filename, $message);
    }

    public function sendOutput($output)
    {
        try {
            print_r($output);
            return true;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            return false;
        }
    }

    protected function errorLog($method = null, $message = null)
    {
        return $this->helperLog->errorLog($method, $message);
    }

    protected function generateClassObject($class = "")
    {
        $object = null;
        try {
            $object = $this->objectManager->get($class);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $object = null;
        } finally {
            return $object;
        }
    }

    protected function getCurrentDate()
    {
        return $this->helperDate->getCurrentDate();
    }

    public function isCurrentDateWithinRange($fromDate, $toDate)
    {
        return $this->helperDate->isCurrentDateWithinRange($fromDate, $toDate);
    }

    protected function getRemoteContent($url)
    {
        return $this->helperUrl->getRemoteContent($url);
    }

    protected function sortDataByColumn(&$data = [], $sortColumn = "qty", $sortDirection = SORT_DESC)
    {
        array_multisort(array_column($data, $sortColumn), $sortDirection, $data);
    }

    protected function getSqlTableName($name = null)
    {
        return $this->helperDb->getSqlTableName($name);
    }

    protected function sqlQuery($sql)
    {
        return $this->helperDb->queryExecute($sql);
    }

    protected function sqlQueryFetchAll($sql, $limit = 0)
    {
        return $this->helperDb->sqlQueryFetchAll($sql, $limit);
    }

    protected function sqlQueryFetchOne($sql)
    {
        return $this->helperDb->sqlQueryFetchOne($sql);
    }

    protected function sqlQueryFetchRow($sql)
    {
        return $this->helperDb->sqlQueryFetchRow($sql);
    }

    protected function urlExists($remoteUrl = "")
    {
        return $this->helperUrl->urlExists($remoteUrl);
    }
}
