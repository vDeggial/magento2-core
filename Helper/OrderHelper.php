<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class OrderHelper extends BaseHelper
{
    protected $tableOrder = null;
    protected $tableOrderItem = null;
    protected $tableOrderAddress = null;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableOrder = $this->getSqlTableName('sales_order');
        $this->tableOrderItem = $this->getSqlTableName('sales_order_item');
        $this->tableOrderAddress = $this->getSqlTableName('sales_order_address');
    }

    public function getBillingFirstName($orderId = null)
    {
        $firstName = null;
        try {
            $sql = "SELECT firstname FROM " . $this->tableOrderAddress . " WHERE address_type LIKE 'billing' AND parent_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $firstName = $result;
        } catch (\Exception $e) {
            $firstName = null;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return trim($firstName);
        }
    }

    public function getBillingLastName($orderId = null)
    {
        $lastName = null;
        try {
            $sql = "SELECT lastname FROM " . $this->tableOrderAddress . " WHERE address_type LIKE 'billing' AND parent_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $lastName = $result;
        } catch (\Exception $e) {
            $lastName = null;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return trim($lastName);
        }
    }

    public function getBillingName($orderId = null)
    {
        $nameFormat = "%s %s";
        $fullName = null;
        try {
            $fullName = sprintf($nameFormat, $this->getBillingFirstName($orderId), $this->getBillingLastName($orderId));
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
            $sql = "SELECT order_id FROM " . $this->tableOrderItem . " where sku LIKE '$productSku'";
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
            $sql = "SELECT email FROM " . $this->tableOrderAddress . " WHERE address_type LIKE 'billing' AND parent_id = $orderId";
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
            $sql = "SELECT qty_canceled FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
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
            $sql = "SELECT qty_ordered FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
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
            $sql = "SELECT qty_refunded FROM " . $this->tableOrderItem . " where order_id = $orderId AND sku LIKE '$productSku'";
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
}
