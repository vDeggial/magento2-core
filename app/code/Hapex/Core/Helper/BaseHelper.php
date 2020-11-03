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

    public function sendOutput($output = null)
    {
        try {
            return parent::sendOutput($output);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            return false;
        }
    }

    public function displayBlock($blockId = null)
    {
        try {
            $this->sendOutput($this->getBlockHtml($blockId));
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        }
    }

    public function getBlockHtml($blockId = null)
    {
        $html  = null;
        try {
            $block = $this->generateClassObject("Magento\Cms\Block\Block");
            $block->setBlockId($blockId);
            $html = $block->toHtml();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
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
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $object;
        }
    }
}
