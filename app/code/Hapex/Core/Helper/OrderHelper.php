<?php

namespace Hapex\Core\Helper;

use Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class OrderHelper extends BaseHelper
{
    protected $tableOrder;
    protected $orderRepository;
    protected $helperItem;
    protected $helperGrid;
    protected $helperAddress;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        OrderItemHelper $helperItem,
        OrderGridHelper $helperGrid,
        OrderAddressHelper $helperAddress,
        OrderRepository $orderRepository
    ) {
        parent::__construct($context, $objectManager);
        $this->helperItem = $helperItem;
        $this->helperGrid = $helperGrid;
        $this->helperAddress = $helperAddress;
        $this->orderRepository = $orderRepository;
        $this->tableOrder = $this->helperDb->getSqlTableName('sales_order');
    }

    public function getOrder($orderId)
    {
        return $this->getOrderById($orderId);
    }

    public function getOrderIdsByCustomerId($customerId = 0)
    {
        $result = null;
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE customer_id = $customerId GROUP BY entity_id ORDER BY created_at DESC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getOrderCreatedDate($orderId = null)
    {
        $date = null;
        try {
            $date =  $this->getOrderFieldValue($orderId, "created_at");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getOrderUpdatedDate($orderId = null)
    {
        $date = null;
        try {
            $date =  $this->getOrderFieldValue($orderId, "updated_at");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getOrderIdCustomerid($orderId = 0)
    {
        $customerId = 0;
        try {
            $customerId = (int) $this->getOrderFieldValue($orderId, "customer_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerId = 0;
        } finally {
            return $customerId;
        }
    }

    public function getOrderCustomerId($order = null)
    {
        $customerId = 0;
        try {
            $customerId = $order->getCustomer()->getId();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerId = 0;
        } finally {
            return $customerId;
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
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $items = [];
        } finally {
            return $items;
        }
    }

    private function getOrderById($orderId)
    {
        $order = null;
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $order = null;
        } finally {
            return $order;
        }
    }

    private function getOrderFieldValue($orderId = null, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableOrder . " where entity_id = $orderId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
