<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Hapex\Core\Helper\DateHelper;

class LogHelper extends AbstractHelper
{
    protected $objectManager;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    public function printLog($filename = null, $message = null)
    {
        return $this->writeLogEntry($filename, $message);
    }

    public function errorLog($method = null, $message = null)
    {
        return $this->printLog("hapex_error_log", "$method :: $message");
    }

    private function writeLogEntry($filename = null, $message = null)
    {
        try {
            $currentDate = $this->objectManager->get(DateHelper::class)->getCurrentDate()->format("Y-m-d h:i:s A T");
            $filePath = $this->objectManager->get(FileHelper::class)->getRootPath() . "/var/log/$filename.log";
            return error_log("[$currentDate] " . $message . "\n", 3, $filePath);;
        } catch (\Exception $e) {
            return false;
        }
    }
}
