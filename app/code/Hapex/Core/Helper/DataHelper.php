<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class DataHelper extends BaseHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
    }

    public function getConfigFlag($path, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getConfigValue($path, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getCsvFileData($fileName, $isFirstRowHeader = false)
    {
        return $this->helperFile->getCsvFileData($fileName, $isFirstRowHeader);
    }

    public function writeCsvFileData($fileName, $data)
    {
        $this->helperFile->writeCsvFileData($fileName, $data);
    }

    public function setCsvLocation($path)
    {
        $this->helperFile->setCsvLocation($path);
    }
}
