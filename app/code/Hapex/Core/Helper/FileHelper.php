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

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        LogHelper $helperLog,
        DirectoryList $directoryList,
        File $fileDriver
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $helperLog;
        $this->directoryList = $directoryList;
        $this->fileDriver = $fileDriver;
    }

    public function getRootPath()
    {
        return $this->directoryList->getRoot();
    }

    public function fileExists($path = null)
    {
        return $this->fileDriver->isExists($path) && $this->fileDriver->isFile($path);
    }

    public function deleteFile($path = null)
    {
        return $this->fileDriver->deleteFile($path);
    }

    public function getFiles($path = null, $extension = null)
    {
        $files = [];
        $fullPath = $this->getRootPath() . $path;
        foreach ($this->fileDriver->readDirectory($fullPath) as $file) {
            $this->processFile($files, $file, $extension);
        }
        return $files;
    }

    private function processFile(&$files = [], $file = null, $extension = null)
    {
        if ($this->getFileExtension($file) === $extension) {
            $files[] = $file;
        }
    }

    public function getFileExtension($filename = null)
    {
        return substr(strrchr($filename,'.'),1);
    }

    public function getFileSize($filename = null)
    {
        return $this->fileExists($filename) ? filesize($filename) : 0;
    }

    public function getRootFilePath($path = null, $filename = null)
    {
        return $this->getRootPath() . "/$path/$filename";
    }

    public function getDirectoryPath($path = null)
    {
        return $this->getRootPath() . "/" . $path;
    }

    public function getFilePath($path = null, $filename = null)
    {
        return "$path/$filename";
    }

    public function getFileContents($path = "", $filename = "")
    {
        return $this->fileDriver->fileGetContents($this->getFilePath($path, $filename));
    }
}
