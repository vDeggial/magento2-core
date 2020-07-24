<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class FileHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $csvDirectory;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $this->objectManager->get("Hapex\Core\Helper\LogHelper");
    }

    public function getRootPath()
    {
        $directory = $this->objectManager->get("Magento\Framework\Filesystem\DirectoryList");
        return $directory->getRoot();
    }

    public function getFileContents($path = "", $filename = "")
    {
        return file_get_contents($this->getRootPath() . "/$path/$filename");
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
        $directoryList = $this->objectManager->get("Magento\Framework\App\Filesystem\DirectoryList");
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
                $csvProcessor = $this->objectManager->get("Magento\Framework\File\Csv");
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
            $csvProcessor = $this->objectManager->get("Magento\Framework\File\Csv");
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
