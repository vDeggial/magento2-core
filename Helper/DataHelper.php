<?php

namespace Hapex\Core\Helper;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;

class DataHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(\Magento\Framework\App\Helper\Context $context) {
        parent::__construct($context);
    }
    
    public function getConfigFlag($path, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }
    
    public function getConfigValue($path, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }
    
     public function printLog($filename,$log)
    {
       $writer = new Stream(BP . "/var/log/$filename.log");
       $logger = new Logger();
       $logger->addWriter($writer);
       $logger->info($log);
    }

}
