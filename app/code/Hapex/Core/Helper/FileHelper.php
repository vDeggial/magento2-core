<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\File\Csv;
use Magento\Framework\ObjectManagerInterface;

class FileHelper extends AbstractHelper
{
    protected $objectManager;
    protected $csvDirectory;
    protected $helperLog;
    protected $directoryList;
    protected $fileDriver;
    protected $csvProcessor;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        LogHelper $helperLog,
        DirectoryList $directoryList,
        File $fileDriver,
        Csv $csvProcessor
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $helperLog;
        $this->directoryList = $directoryList;
        $this->fileDriver = $fileDriver;
        $this->csvProcessor = $csvProcessor;
    }

    public function getRootPath()
    {
        return $this->directoryList->getRoot();
    }

    public function fileExists($path = null)
    {
        return $this->fileDriver->isExists($path);
    }

    public function deleteFile($path = null)
    {
        return $this->fileDriver->deleteFile($path);
    }

    public function getFiles($path = null, $extension = null)
    {
        $files = array();
        $fullPath = $this->getRootPath() . $path;
        foreach ($this->fileDriver->readDirectory($fullPath) as $file) {
            if ($this->getFileExtension($file) === $extension) {
                $files[] = $file;
            }
        }
        return $files;
    }

    public function getFileExtension($filename = null)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public function getFileSize($filename = null)
    {
        $fileSize = 0;
        if ($this->fileExists($filename)) {
            $fileSize = filesize($filename);
        }
        return $fileSize;
    }

    public function getFileContents($path = "", $filename = "")
    {
        return $this->fileDriver->fileGetContents($this->getRootPath() . "/$path/$filename");
    }

    public function getCsvFileData($fileName, $isFirstRowHeader = false)
    {
        return $this->getCsvDataFile($this->csvDirectory . "/" . "$fileName", $isFirstRowHeader);
    }

    public function writeCsvFileData($fileName, $data)
    {
        $this->writeCsvDataFile($this->csvDirectory, $fileName, $data);
    }

    public function setCsvLocation($path)
    {
        $this->csvDirectory = $this->directoryList->getPath(DirectoryList::PUB) . "/" . $path;
    }

    protected function getCsvDataFile($fileName, $isFirstRowHeader = false)
    {
        $data = [];
        try {
            if ($this->fileExists($fileName)) {
                $data = $this->csvProcessor->getData($fileName);
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
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
            $this->csvProcessor->setEnclosure('"')->setDelimiter(',')->saveData("$path/$fileName", $data);
            $success = true;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $success = false;
        } finally {
            return $success;
        }
    }
}
