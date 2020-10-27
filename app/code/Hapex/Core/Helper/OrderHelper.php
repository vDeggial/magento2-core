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
        $result = [];
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE customer_id = $customerId GROUP BY entity_id ORDER BY created_at DESC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = [];
        } finally {
            return $result;
        }
    }

    public function getOrderIdByIncrementId($incrementId = null)
    {
        $result = 0;
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE increment_id like '$incrementId'";
            $result = (int) $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = 0;
        } finally {
            return $result;
        }
    }

    public function getOrderAppliedRuleIds($orderId = 0)
    {
        $ruleIds = null;
        try {
            $ruleIds = $this->getOrderFieldValue($orderId, "applied_rule_ids");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $ruleIds = null;
        } finally {
            return $ruleIds;
        }
    }

    public function getOrderCreatedDate($orderId = 0)
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

    public function getOrderUpdatedDate($orderId = 0)
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

    public function getOrderCouponCode($orderId = 0)
    {
        $code = null;
        try {
            $code = $this->getOrderFieldValue($orderId, "coupon_code");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $code = null;
        } finally {
            return $code;
        }
    }

    public function getOrderCustomerId($orderId = 0)
    {
        $customerId = 0;
        try {
            $customerId = $this->getOrderFieldValue($orderId, "customer_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerId = 0;
        } finally {
            return $customerId;
        }
    }

    public function getOrderCustomerGroupId($orderId = 0)
    {
        $groupId = 0;
        try {
            $groupId = $this->getOrderFieldValue($orderId, "customer_group_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $groupId = 0;
        } finally {
            return $groupId;
        }
    }

    public function getOrderDiscountAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "discount_amount");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getOrderGrandTotal($orderId = 0)
    {
        $total = 0;
        try {
            $total = $this->getOrderFieldValue($orderId, "grand_total");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getOrderIncrementId($orderId = 0)
    {
        $incrementId = null;
        try {
            $incrementId = $this->getOrderFieldValue($orderId, "increment_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $incrementId = null;
        } finally {
            return $incrementId;
        }
    }

    public function getCustomerId($order = null)
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

    public function getOrderIsVirtual($orderId = 0)
    {
        $isVirtual = 0;
        try {
            $isVirtual = (int) $this->getOrderFieldValue($orderId, "is_virtual");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isVirtual = 0;
        } finally {
            return $isVirtual;
        }
    }

    public function getOrderShippingAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "shipping_amount");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getOrderShippingMethod($orderId = 0)
    {
        $method = null;
        try {
            $method = $this->getOrderFieldValue($orderId, "coupon_code");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $method = null;
        } finally {
            return $method;
        }
    }

    public function getOrderState($orderId = 0)
    {
        $state = null;
        try {
            $state = $this->getOrderFieldValue($orderId, "state");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $state = null;
        } finally {
            return $state;
        }
    }

    public function getOrderStatus($orderId = 0)
    {
        $status = null;
        try {
            $status = $this->getOrderFieldValue($orderId, "status");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = null;
        } finally {
            return $status;
        }
    }

    public function getOrderSubtotal($orderId = 0)
    {
        $total = 0;
        try {
            $total = $this->getOrderFieldValue($orderId, "subtotal");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getOrderTaxAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "tax_amount");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getOrderTotalPaid($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "total_paid");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getOrderTotalItemCount($orderId = 0)
    {
        $count = 0;
        try {
            $count = (int) $this->getOrderFieldValue($orderId, "total_item_count");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $count = 0;
        } finally {
            return $count;
        }
    }

    public function getOrderTotalQtyOrdered($orderId = 0)
    {
        $count = 0;
        try {
            $count = (int) $this->getOrderFieldValue($orderId, "total_qty_ordered");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $count = 0;
        } finally {
            return $count;
        }
    }

    public function getOrderCustomerIsGuest($orderId = 0)
    {
        $isGuest = 0;
        try {
            $isGuest = (int) $this->getOrderFieldValue($orderId, "customer_is_guest");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isGuest = 0;
        } finally {
            return $isGuest;
        }
    }

    public function isGuestOrder($order = null)
    {
        $isGuestOrder = false;
        try {
            $isGuestOrder = $order->getCustomerIsGuest();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isGuestOrder = false;
        } finally {
            return $isGuestOrder;
        }
    }

    protected function getOrderItems($order = null)
    {
        $items = [];
        try {
            $orderItems = $order->getItems();
            $items = array_filter($orderItems, function ($item) {
                return !$item->isDeleted() && !$item->getParentItem();
            });
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $items = [];
        } finally {
            return $items;
        }
    }

    protected function getOrderItemsMergeSku($order = null)
    {
        $orderItems  = $this->getOrderItems($order);
        $items = [];
        try {
            array_walk($orderItems, function ($item) use (&$items) {
                switch ($this->getArrayValue($items, $item->getSku()) !== null) {
                    case true:
                        $items[$item->getSku()]->setQtyInvoiced($items[$item->getSku()]->getQtyInvoiced() + $item->getQtyInvoiced());
                        break;

                    default:
                        $items[$item->getSku()] = $item;
                        break;
                }
            });
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $items = [];
        } finally {
            return $items;
        }
    }

    protected function getOrderById($orderId = 0)
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

    protected function getOrderFieldValue($orderId = 0, $fieldName = null)
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
