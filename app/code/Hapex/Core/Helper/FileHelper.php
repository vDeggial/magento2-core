<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\File\Csv;
use Magento\Framework\ObjectManagerInterface;

class FileHelper extends AbstractHelper
{
    protected $objectManager;
    protected $csvDirectory;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, LogHelper $helperLog)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $helperLog;
    }

    public function getRootPath()
    {
        $directory = $this->objectManager->get(DirectoryList::class);
        return $directory->getRoot();
    }

    public function getFileContents($path = "", $filename = "")
    {
        $fileDriver = $this->objectManager->get(File::class);
        return $fileDriver->fileGetContents($this->getRootPath() . "/$path/$filename");
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
        $directoryList = $this->objectManager->get(DirectoryList::class);
        $this->csvDirectory = $directoryList->getPath(DirectoryList::PUB) . "/" . $path;
        // if (!is_dir($this->csvDirectory)) {
        //     mkdir($this->csvDirectory, 0777, true);
        // }
    }

    protected function getCsvDataFile($fileName, $isFirstRowHeader = false)
    {
        $data = [];
        try {
            $fileDriver = $this->objectManager->get(File::class);
            if ($fileDriver->isExists($fileName)) {
                $csvProcessor = $this->objectManager->get(Csv::class);
                $data = $csvProcessor->getData($fileName);
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
            $csvProcessor = $this->objectManager->get(Csv::class);
            $csvProcessor->setEnclosure('"')->setDelimiter(',')->saveData("$path/$fileName", $data);
            $success = true;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $success = false;
        } finally {
            return $success;
        }
    }
}
