<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class DataHelper extends BaseHelper
{
    protected $csvDirectory;
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
        return $this->getCsvDataFile($this->csvDirectory . "/" . "$fileName", $isFirstRowHeader);
    }

    public function setCsvLocation($path)
    {
        $directoryList = $this->generateClassObject("Magento\Framework\App\Filesystem\DirectoryList");
        $this->csvDirectory = $directoryList->getPath(DirectoryList::PUB) . "/" . $path;
        // if (!is_dir($this->csvDirectory)) {
            //     mkdir($this->csvDirectory, 0777, true);
            // }
    }

    protected function getCsvDataFile($fileName, $isFirstRowHeader = false)
    {
        $data = [];
        try {
            if (file_exists($fileName)) {
                $csvProcessor = $this->generateClassObject("Magento\Framework\File\Csv");
                $data = $csvProcessor->getData($fileName);
            }
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $data = [];
        } finally {
            if (!empty($data) && $isFirstRowHeader) {
                array_shift($data);
            }
            return $data;
        }
    }

    protected function writeCsvDataFile($path, $fileName, $data = [])
    {
        $success = false;
        try {
            $csvProcessor = $this->generateClassObject("Magento\Framework\File\Csv");
            $csvProcessor->saveData("$path/$fileName", $data);
            $success = true;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $success = false;
        } finally {
            return $success;
        }
    }
}
