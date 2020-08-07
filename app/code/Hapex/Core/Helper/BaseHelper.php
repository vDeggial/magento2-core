<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

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
        $this->helperDb = $this->generateClassObject(\Hapex\Core\Helper\DbHelper::class);
        $this->helperLog = $this->generateClassObject(\Hapex\Core\Helper\LogHelper::class);
        $this->helperFile = $this->generateClassObject(\Hapex\Core\Helper\FileHelper::class);
        $this->helperDate = $this->generateClassObject(\Hapex\Core\Helper\DateHelper::class);
        $this->helperUrl = $this->generateClassObject(\Hapex\Core\Helper\UrlHelper::class);
        parent::__construct($context);
    }

    public function getLogHelper()
    {
        return $this->helperLog;
    }

    public function getDbHelper()
    {
        return $this->helperDb;
    }

    public function getFileHelper()
    {
        return $this->helperFile;
    }

    public function getDateHelper()
    {
        return $this->helperDate;
    }

    public function getUrlHelper()
    {
        return $this->helperUrl;
    }

    public function getFileContents($path = null, $filename = null)
    {
        return $this->helperFile->getFileContents($path, $filename);
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

    protected function generateClassObject($class = null)
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

    protected function sortDataByColumn(&$data = [], $sortColumn = "qty", $sortDirection = SORT_DESC)
    {
        array_multisort(array_column($data, $sortColumn), $sortDirection, $data);
    }
}
