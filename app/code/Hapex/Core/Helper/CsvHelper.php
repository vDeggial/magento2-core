<?php

namespace Hapex\Core\Helper;

use Magento\Framework\File\Csv;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class CsvHelper extends BaseHelper
{
    protected $objectManager;
    protected $csvDirectory;
    protected $csvProcessor;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Csv $csvProcessor
    ) {
        parent::__construct($context, $objectManager);
        $this->csvProcessor = $csvProcessor;
    }

    public function getCsvFileData($fileName = null, $isFirstRowHeader = false)
    {
        return $this->getCsvDataFile($this->helperFile->getFilePath($this->csvDirectory, $fileName), $isFirstRowHeader);
    }

    public function writeCsvFileData($fileName = null, $data = [])
    {
        $this->writeCsv($this->csvDirectory, $fileName, $data);
    }

    public function setCsvLocation($path = null)
    {
        $this->csvDirectory = $this->helperFile->getDirectoryPath(DirectoryList::PUB. "/" .$path);
    }

    protected function getCsvDataFile($fileName = null, $isFirstRowHeader = false)
    {
        $data = [];
        try {
            $data = $this->getCsv($fileName);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $data = [];
        } finally {
            $this->shiftData($data, $isFirstRowHeader);
            return $data;
        }
    }

    private function shiftData(&$data = [], $isFirstRowHeader = false)
    {
        if (!empty($data) && $isFirstRowHeader) {
            array_shift($data);
        }
    }

    private function getCsv($fileName = null)
    {
        return $this->helperFile->fileExists($fileName) ? array_filter($this->csvProcessor->getData($fileName)) : [];
    }

    protected function writeCsv($path = null, $fileName = null, $data = [])
    {
        $success = false;
        try {
            $this->csvProcessor->setEnclosure('"')->setDelimiter(',')->saveData($this->helperFile->getFilePath($path, $fileName), $data);
            $success = true;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $success = false;
        } finally {
            return $success;
        }
    }
}
