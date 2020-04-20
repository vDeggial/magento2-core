<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class OrderHelper extends BaseHelper
{
    protected $tableOrder = null;
    protected $tableOrderGrid = null;
    protected $tableOrderItem = null;
    protected $tableOrderAddress = null;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableOrder = $this->getSqlTableName('sales_order');
        $this->tableOrderGrid = $this->getSqlTableName('sales_order_grid');
        $this->tableOrderItem = $this->getSqlTableName('sales_order_item');
        $this->tableOrderAddress = $this->getSqlTableName('sales_order_address');
    }

    public function getBillingName($orderId = null)
    {
        $fullName = null;
        try {
            $sql = "SELECT billing_name FROM " . $this->tableOrderGrid . " WHERE entity_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $fullName = $result;
        } catch (\Exception $e) {
            $fullName = null;
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $fullName;
        }
    }

    public function getByProductSku($productSku = null)
    {
        $result = null;
        try {
            $sql = "SELECT distinct order_id FROM " . $this->tableOrderItem . " where sku LIKE '$productSku'";
            $result = $this->sqlQueryFetchAll($sql);
        } catch (\Exception $e) {
            $result = null;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $result;
        }
    }

    public function getBillingEmail($orderId = null)
    {
        $email = null;
        try {
            $sql = "SELECT customer_email FROM " . $this->tableOrderGrid . " WHERE entity_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $email = $result;
        } catch (\Exception $e) {
            $email = null;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $email;
        }
    }

    public function getQtyCanceled($orderId = null, $productSku = null)
    {
        $qty = 0;
        try {
            $sql = "SELECT sum(qty_canceled) FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
            $result = $this->sqlQueryFetchOne($sql);
            $qty = (int)$result;
        } catch (\Exception $e) {
            $qty = 0;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $qty;
        }
    }

    public function getQtyOrdered($orderId = null, $productSku = null)
    {
        $qty = 0;
        try {
            $sql = "SELECT sum(qty_ordered) FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
            $result = $this->sqlQueryFetchOne($sql);
            $qty = (int)$result;
        } catch (\Exception $e) {
            $qty = 0;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $qty;
        }
    }

    public function getQtyRefunded($orderId = null, $productSku = null)
    {
        $qty = 0;
        try {
            $sql = "SELECT sum(qty_refunded) FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
            $result = $this->sqlQueryFetchOne($sql);
            $qty = (int)$result;
        } catch (\Exception $e) {
            $qty = 0;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $qty;
        }
    }

    public function getShippingName($orderId = null)
    {
        $fullName = null;
        try {
            $sql = "SELECT shipping_name FROM " . $this->tableOrderGrid . " WHERE entity_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $fullName = $result;
        } catch (\Exception $e) {
            $fullName = null;
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $fullName;
        }
    }
}
