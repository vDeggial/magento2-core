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

    public function getCsvDataFile($fileName)
    {
        $data = [];
        try {
            $csvProcessor = $this->generateClassObject("Magento\Framework\File\Csv");
            $data = $csvProcessor->getData($fileName);
        } catch (\Exception $e) {
            $data = [];
        } finally {
            return $data;
        }
    }

    public function writeCsvDataFile($path, $fileName, $data = [])
    {
        $success = false;
        try {
            $csvProcessor = $this->generateClassObject("Magento\Framework\File\Csv");
            $csvProcessor->saveData("$path/$fileName", $data);
            $success = true;
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return $success;
        }
    }
}
