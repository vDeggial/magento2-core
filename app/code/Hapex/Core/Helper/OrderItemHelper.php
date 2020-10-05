<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class OrderItemHelper extends BaseHelper
{
    protected $tableOrderItem;
    protected $helperAddress;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, OrderAddressHelper $helperAddress)
    {
        parent::__construct($context, $objectManager);
        $this->tableOrderItem = $this->helperDb->getSqlTableName('sales_order_item');
        $this->helperAddress = $helperAddress;
    }


    public function getOrderIdsWithSku($productSku = null)
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

    public function getItemIds($orderId = 0)
    {
        $ids = [];
        try {
            $sql = "SELECT item_id FROM" . $this->tableOrderItem . " WHERE order_id = $orderId";
            $ids = array_column($this->helperDb->sqlQueryFetchAll($sql), "item_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $ids = [];
        } finally {
            return $ids;
        }
    }

    public function getItemTotalQtyCanceled($orderId = 0, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueBySku($orderId, $productSku, "sum(qty_canceled)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemTotalQtyOrdered($orderId = 0, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueBySku($orderId, $productSku, "sum(qty_ordered)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemTotalQtyRefunded($orderId = 0, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueBySku($orderId, $productSku, "sum(qty_refunded)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemTotalQtyShipped($orderId = 0, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueBySku($orderId, $productSku, "sum(qty_shipped)");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getOrderItemData($order = null, $item = null)
    {
        $info = [];
        try {
            $info["fullName"] = $this->helperAddress->getOrderCustomerName($order);
            $info["email"] = $this->helperAddress->getOrderCustomerEmail($order);
            $info["qtyOrdered"] = (int) $item->getQtyOrdered();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $info = [];
        } finally {
            return $info;
        }
    }

    public function getItemId($orderId = 0, $productSku = null)
    {
        $itemId = 0;
        try {
            $itemId = (int) $this->getItemFieldValueBySku($orderId, $productSku, "item_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $itemId = 0;
        } finally {
            return $itemId;
        }
    }

    public function getItemProductId($itemId = 0)
    {
        $productId = 0;
        try {
            $productId = (int) $this->getItemFieldValueById($itemId, "product_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productId = 0;
        } finally {
            return $productId;
        }
    }

    public function getItemSku($itemId = 0)
    {
        $sku = null;
        try {
            $sku = $this->getItemFieldValueById($itemId, "sku");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $sku = null;
        } finally {
            return $sku;
        }
    }

    public function getItemProductName($itemId = 0)
    {
        $name = null;
        try {
            $name = $this->getItemFieldValueByid($itemId, "name");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getItemProductType($itemId = 0)
    {
        $type = null;
        try {
            $type = $this->getItemFieldValueById($itemId, "product_type");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $type = null;
        } finally {
            return $type;
        }
    }


    private function getItemFieldValueBySku($orderId = 0, $productSku = null, $fieldName = null)
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

    private function getItemFieldValueById($itemId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableOrderItem . " where item_id = $itemId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
