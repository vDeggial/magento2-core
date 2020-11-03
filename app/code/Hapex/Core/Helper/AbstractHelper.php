<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\DataObject;

abstract class AbstractHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
    }

    protected function getArrayValue($array = [], $index = 0, $defaultValue = null)
    {
        return $array[$index] ?? $defaultValue;
    }

    public function sendOutput($output = null)
    {
        try {
            print_r($output);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateClassObject($class = null)
    {
        $object = $this->objectManager->create(DataObject::class);
        try {
            $object = $this->objectManager->get($class);
        } catch (\Exception $e) {
            $object = $this->objectManager->create(DataObject::class);
        } finally {
            return $object;
        }
    }

    public function sortDataByColumn(&$data = [], $sortColumn = "qty", $sortDirection = SORT_DESC)
    {
        array_multisort(array_column($data, $sortColumn), $sortDirection, $data);
    }

    public function sortDataBy2Columns(&$data = [], $sortColumn = "qty", $sortDirection = SORT_DESC, $sortColumn2 = "qty", $sortDirection2 = SORT_DESC)
    {
        array_multisort(array_column($data, $sortColumn), $sortDirection, array_column($data, $sortColumn2), $sortDirection2, $data);
    }
}
