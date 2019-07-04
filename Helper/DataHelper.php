<?php

namespace Hapex\Core\Helper;
use Magento\Framework\App\Helper\Context;

class DataHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    public function __construct(
        Context $context) {
        parent::__construct($context);
    }
    
    
    protected function getConfigFlag($path, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }
    
    protected function getConfigValue($path, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }

}
