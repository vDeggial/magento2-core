<?php

namespace Hapex\Core\Helper;
use Magento\Framework\App\Helper\Context;

class DataHelper extends BaseHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
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

}
