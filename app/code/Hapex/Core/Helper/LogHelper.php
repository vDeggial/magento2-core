<?php
namespace Hapex\Core\Helper;

use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class LogHelper extends \Magento\Framework\App\Helper\AbstractHelper
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
            $writer = new Stream(BP . "/var/log/$filename.log");
            $logger = new Logger();
            $formatter = new Formatter\Simple();
            $formatter->setDateTimeFormat("Y-m-d H:i:s T");
            $writer->setFormatter($formatter);
            $logger->addWriter($writer);
            $logger->info($message);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}