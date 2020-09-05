<?php

namespace Hapex\Core\Helper;

use Magento\Store\Model\ScopeInterface;

class DataHelper extends BaseHelper
{

    public function getConfigFlag($path = null, $scopeCode = null)
    {
        $isSetFlag = false;
        try {
            $isSetFlag = $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $scopeCode);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isSetFlag = false;
        } finally {
            return $isSetFlag;
        }
    }

    public function getConfigValue($path = null, $scopeCode = null)
    {
        $value = null;
        try {
            $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $scopeCode);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }
}
