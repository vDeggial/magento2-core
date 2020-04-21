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

    public function getOrder($orderId)
    {
        $order = null;
        try {
            $orderRepository = $this->generateClassObject("Magento\Sales\Model\OrderRepository");
            $order = $orderRepository->get($orderId);
        } catch (\Exception $e) {
            $order = null;
        } finally {
            return $order;
        }
    }

    public function getOrderDataBySku($productSku = null)
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

    public function getOrderEmail($data)
    {
        switch (true) {
        case is_numeric($data):
          return $this->getCustomerEmail($data);

        case is_object($data):
          return $this->getOrderCustomerEmail($data);

        default:
          return null;
      }
    }

    public function getOrderName($data)
    {
        switch (true) {
        case is_numeric($data):
          return $this->getBillingName($data);

        case is_object($data):
          return $this->getOrderCustomerName($data);

        default:
          return null;
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

    protected function getBillingName($orderId = null)
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

    protected function getCustomerEmail($orderId = null)
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

    protected function getOrderAddress($order = null)
    {
        $address = null;
        try {
            $address = $order->getBillingAddress();
        } catch (\Exception $e) {
            $address = null;
        } finally {
            return $address;
        }
    }

    protected function getOrderCustomerName($order = null)
    {
        $customerName = null;
        try {
            $address = $this->getOrderAddress($order);
            $customerName = $address->getName();
        } catch (\Exception $e) {
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    protected function getOrderCustomerEmail($order = null)
    {
        $customerEmail = null;
        try {
            $address = $this->getOrderAddress($order);
            $customerEmail = $address->getEmail();
        } catch (\Exception $e) {
            $customerEmail = null;
        } finally {
            return $customerEmail;
        }
    }

    protected function getShippingName($orderId = null)
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
