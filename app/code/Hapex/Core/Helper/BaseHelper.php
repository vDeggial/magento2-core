<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class BaseHelper extends AbstractHelper
{
    protected $objectManager;
    protected $helperDb;
    protected $helperLog;
    protected $helperFile;
    protected $helperDate;
    protected $helperUrl;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->helperDb = $this->generateClassObject(DbHelper::class);
        $this->helperLog = $this->generateClassObject(LogHelper::class);
        $this->helperFile = $this->generateClassObject(FileHelper::class);
        $this->helperDate = $this->generateClassObject(DateHelper::class);
        $this->helperUrl = $this->generateClassObject(UrlHelper::class);
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

    public function errorLog($method = null, $message = null)
    {
        $this->helperLog->errorLog($method, $message);
    }

    public function sendOutput($output = null)
    {
        try {
            return parent::sendOutput($output);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            return false;
        }
    }

    public function displayBlock($blockId = null)
    {
        try {
            $this->sendOutput($this->getBlockHtml($blockId));
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
        }
    }

    public function getBlockHtml($blockId = null)
    {
        $html  = null;
        try {
            $block = $this->generateClassObject("Magento\Cms\Block\Block");
            $block->setBlockId($blockId);
            $html = $block->toHtml();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $html = null;
        } finally {
            return $html;
        }
    }

    public function generateClassObject($class = null)
    {
        $object = null;
        try {
            $object = parent::generateClassObject($class);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
        } finally {
            return $object;
        }
    }

    public function splitArray(array $array = [], bool $isChunksNum = true, int $num = 1, bool $preserve_keys = false): array
    {
        $arraySize = count($array);
        switch (true) {
            case $arraySize > 0 && $num > 0:
                $size = (int)ceil(($arraySize / $num));
                return array_chunk($array, ($isChunksNum ? $size : $num), $preserve_keys);

            default:
                return [];
        }
    }
}
