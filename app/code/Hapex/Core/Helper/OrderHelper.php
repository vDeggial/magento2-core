<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class OrderHelper extends BaseHelper
{
    protected $tableOrder;
    protected $tableOrderGrid;
    protected $tableOrderItem;
    protected $tableOrderAddress;

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
            $this->errorLog($e->getMessage());
            $order = null;
        } finally {
            return $order;
        }
    }

    public function getOrderIdsByProductSku($productSku = null)
    {
        $result = null;
        try {
            $sql = "SELECT order_id FROM " . $this->tableOrderItem . " WHERE sku LIKE '$productSku' GROUP BY order_id";
            $result = array_column($this->sqlQueryFetchAll($sql), "order_id");
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $result = null;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $result;
        }
    }

    public function getOrderIdsByCustomerId($customerId = 0)
    {
        $result = null;
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE customer_id = $customerId GROUP BY entity_id ORDER BY created_at DESC";
            $result = array_column($this->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $result = null;
            $this->printLog("errors", $sql);
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $result;
        }
    }

    public function getOrderCreatedDate($orderId = null)
    {
        $date = null;
        try {
            $sql = "SELECT created_at FROM " . $this->tableOrder . " where order_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $date = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getOrderUpdatedDate($orderId = null)
    {
        $date = null;
        try {
            $sql = "SELECT updated_at FROM " . $this->tableOrder . " where order_id = $orderId";
            $result = $this->sqlQueryFetchOne($sql);
            $date = (string)$result;
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $date = null;
        } finally {
            return $date;
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
            $this->errorLog($e->getMessage());
            $qty = 0;
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
            $this->errorLog($e->getMessage());
            $qty = 0;
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
            $this->errorLog($e->getMessage());
            $qty = 0;
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
            $this->errorLog($e->getMessage());
            $fullName = null;
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
            $this->errorLog($e->getMessage());
            $email = null;
        } finally {
            return $email;
        }
    }

    protected function getOrderBillingAddress($order = null)
    {
        $address = null;
        try {
            $address = $order->getBillingAddress();
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $address = null;
        } finally {
            return $address;
        }
    }

    protected function getOrderShippingAddress($order = null)
    {
        $address = null;
        try {
            $address = $order->getShippingAddress();
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $address = null;
        } finally {
            return $address;
        }
    }

    protected function getOrderCustomerName($order = null)
    {
        $customerName = null;
        try {
            $address = $this->getOrderBillingAddress($order);
            $customerName = $address->getName();
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    protected function getOrderCustomerEmail($order = null)
    {
        $customerEmail = null;
        try {
            $address = $this->getOrderBillingAddress($order);
            $customerEmail = $address->getEmail();
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            $customerEmail = null;
        } finally {
            return $customerEmail;
        }
    }

    protected function getOrderItems($order = null)
    {
        $items = [];
        try {
            foreach ($order->getItems() as $item) {
                if (!$item->isDeleted() && !$item->getParentItem()) {
                    $items[] = $item;
                }
            }
        } catch (\Exception $e) {
            $items = [];
        } finally {
            return $items;
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
            $this->errorLog($e->getMessage());
            $fullName = null;
            $this->printLog("errors", $e->getMessage());
        } finally {
            return $fullName;
        }
    }
}
