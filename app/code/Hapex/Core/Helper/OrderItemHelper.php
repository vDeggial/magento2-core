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
            $sql = "SELECT item_id FROM " . $this->tableOrderItem . " WHERE order_id = $orderId group by item_id";
            $ids = array_column($this->helperDb->sqlQueryFetchAll($sql), "item_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $ids = [];
        } finally {
            return $ids;
        }
    }

    public function getItemIdsFromOrders($orderIds = [])
    {
        $ids = [];
        try {
            if (!empty($orderIds)) {
                $orderIdString = implode(",", $orderIds);
                $sql = "SELECT item_id FROM " . $this->tableOrderItem . " WHERE order_id in ($orderIdString) group by item_id";
                $ids = array_column($this->helperDb->sqlQueryFetchAll($sql), "item_id");
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $ids = [];
        } finally {
            return $ids;
        }
    }

    public function getItemCreatedDate($itemId = 0)
    {
        $date = null;
        try {
            $date =  $this->getItemFieldValueById($itemId, "created_at");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getItemUpdatedDate($itemId = 0)
    {
        $date = null;
        try {
            $date =  $this->getItemFieldValueById($itemId, "updated_at");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getItemQtyCanceled($itemId = 0)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueById($itemId, "qty_canceled");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemQtyOrdered($itemId = 0)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueById($itemId, "qty_ordered");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemQtyInvoiced($itemId = 0)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueById($itemId, "qty_invoiced");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemQtyRefunded($itemId = 0)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueById($itemId, "qty_refunded");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
        }
    }

    public function getItemQtyShipped($itemId = 0)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueById($itemId, "qty_shipped");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = 0;
        } finally {
            return $qty;
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

    public function getItemTotalQtyInvoiced($orderId = 0, $productSku = null)
    {
        $qty = 0;
        try {
            $qty = (int) $this->getItemFieldValueBySku($orderId, $productSku, "sum(qty_invoiced)");
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
            $info["qtyInvoiced"] = (int) $item->getQtyInvoiced();
            $info["dateCreated"] = $this->helperDate->getCurrentDate()->format("Y-m-d H:i:s");
            $info["dateUpdated"] = $order->getUpdatedAt();
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

    public function getItemOrderId($itemId = 0)
    {
        $orderId = 0;
        try {
            $orderId = (int) $this->getItemFieldValueById($itemId, "order_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $orderId = 0;
        } finally {
            return $orderId;
        }
    }

    public function getItemIsVirtual($itemId = 0)
    {
        $isVirtual = 0;
        try {
            $isVirtual = (int) $this->getItemFieldValueById($itemId, "is_virtual");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isVirtual = 0;
        } finally {
            return $isVirtual;
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

    public function getItemOptions($itemId = 0)
    {
        $options = [];
        try {
            $options = $this->getArrayValue(json_decode($this->getItemFieldValueById($itemId, "product_options"), true), "options", []);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $options = [];
        } finally {
            return $options;
        }
    }


    private function getItemFieldValueBySku($orderId = 0, $productSku = null, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableOrderItem . " where order_id in($orderId) AND sku in('$productSku')";
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
            $sql = "SELECT $fieldName FROM " . $this->tableOrderItem . " where item_id in($itemId)";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
