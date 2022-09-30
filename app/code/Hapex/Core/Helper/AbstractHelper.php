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
        $this->sortDataByColumns($data, $sortColumn, $sortDirection);
    }

    public function sortDataBy2Columns(&$data = [], $sortColumn = "qty", $sortDirection = SORT_DESC, $sortColumn2 = "qty", $sortDirection2 = SORT_DESC)
    {
        $this->sortDataByColumns($data, $sortColumn, $sortDirection, $sortColumn2, $sortDirection2);
    }

    public function sortDataByColumns(&$data = [], ...$args)
    {
        $params = [];
        $is_empty = false;

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $col = array_column($data, $arg);
                if (count($col) > 0) {
                    $params[] = array_column($data, $arg);
                    $is_empty = false;
                } else {
                    $is_empty = true;
                }
            } else {
                if (!$is_empty) $params[] = $arg;
            }
        }

        if (!empty($params)) {
            $params[] = &$data;
            call_user_func_array('array_multisort', $params);
            $data = array_pop($params);
        }
    }
}
