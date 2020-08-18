<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class OrderItemHelper extends BaseHelper
{
    protected $tableOrderItem;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableOrderItem = $this->helperDb->getSqlTableName('sales_order_item');
    }


    public function getOrderIdsByProductSku($productSku = null)
    {
        $result = null;
        try {
            $sql = "SELECT order_id FROM " . $this->tableOrderItem . " WHERE sku LIKE '$productSku' GROUP BY order_id";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "order_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getQtyCanceled($orderId = null, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getOrderItemFieldValue($orderId, $productSku, "sum(qty_canceled)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getQtyOrdered($orderId = null, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getOrderItemFieldValue($orderId, $productSku, "sum(qty_ordered)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getQtyRefunded($orderId = null, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getOrderItemFieldValue($orderId, $productSku, "sum(qty_refunded)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }


    private function getOrderItemFieldValue($orderId = null, $productSku = null, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
