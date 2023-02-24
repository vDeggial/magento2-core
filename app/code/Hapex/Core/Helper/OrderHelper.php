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
        return $this->getById($orderId);
    }

    public function getOrderIdsByCustomerId($customerId = 0)
    {
        $result = [];
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE customer_id = $customerId GROUP BY entity_id ORDER BY created_at DESC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = [];
        } finally {
            return $result;
        }
    }

    public function getOrderIdsBetweenDates($dateFrom = "2022-07-01", $dateTo = null)
    {
        $dateFrom = isset($dateFrom) ? "'$dateFrom'" : "2022-07-01";
        $dateTo = isset($dateTo) ? "'$dateTo'" : "NOW()";
        $result = [];
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE created_at >= $dateFrom and created_at <= $dateTo ORDER BY created_at ASC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = 0;
        } finally {
            return $result;
        }
    }

    public function getAppliedRuleIds($orderId = 0)
    {
        $ruleIds = null;
        try {
            $ruleIds = $this->getOrderFieldValue($orderId, "applied_rule_ids");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $ruleIds = null;
        } finally {
            return $ruleIds;
        }
    }

    public function getCreatedDate($orderId = 0)
    {
        $date = null;
        try {
            $date =  (string) $this->getOrderFieldValue($orderId, "created_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getUpdatedDate($orderId = 0)
    {
        $date = null;
        try {
            $date =  (string) $this->getOrderFieldValue($orderId, "updated_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getCouponCode($orderId = 0)
    {
        $code = null;
        try {
            $code = $this->getOrderFieldValue($orderId, "coupon_code");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $code = null;
        } finally {
            return $code;
        }
    }

    public function getCustomerId($orderId = 0)
    {
        $customerId = 0;
        try {
            $customerId = $this->getOrderFieldValue($orderId, "customer_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerId = 0;
        } finally {
            return $customerId;
        }
    }

    public function getCustomerGroupId($orderId = 0)
    {
        $groupId = 0;
        try {
            $groupId = $this->getOrderFieldValue($orderId, "customer_group_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $groupId = 0;
        } finally {
            return $groupId;
        }
    }

    public function getDiscountAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "discount_amount");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getRefundAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = (float) $this->getOrderFieldValue($orderId, "total_refunded");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getGiftCardAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = (float) $this->getOrderFieldValue($orderId, "gift_card_amount");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getGiftCreditAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = (float) $this->getOrderFieldValue($orderId, "gift_credit_amount");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getGrandTotal($orderId = 0)
    {
        $total = 0;
        try {
            $total = $this->getOrderFieldValue($orderId, "grand_total");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getIncrementId($orderId = 0)
    {
        $incrementId = null;
        try {
            $incrementId = $this->getOrderFieldValue($orderId, "increment_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $incrementId = null;
        } finally {
            return $incrementId;
        }
    }

    public function getOrderCustomerId($order = null)
    {
        $customerId = 0;
        try {
            $customerId = $order->getCustomer()->getId();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerId = 0;
        } finally {
            return $customerId;
        }
    }

    public function getIsVirtual($orderId = 0)
    {
        $isVirtual = 0;
        try {
            $isVirtual = (int) $this->getOrderFieldValue($orderId, "is_virtual");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isVirtual = 0;
        } finally {
            return $isVirtual;
        }
    }

    public function getShippingAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "shipping_amount");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getShippingMethod($orderId = 0)
    {
        $method = null;
        try {
            $method = $this->getOrderFieldValue($orderId, "coupon_code");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $method = null;
        } finally {
            return $method;
        }
    }

    public function getState($orderId = 0)
    {
        $state = null;
        try {
            $state = $this->getOrderFieldValue($orderId, "state");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $state = null;
        } finally {
            return $state;
        }
    }

    public function getStatus($orderId = 0)
    {
        $status = null;
        try {
            $status = $this->getOrderFieldValue($orderId, "status");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = null;
        } finally {
            return $status;
        }
    }

    public function getSubtotal($orderId = 0)
    {
        $total = 0;
        try {
            $total = $this->getOrderFieldValue($orderId, "subtotal");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getTaxAmount($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "tax_amount");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getTotalPaid($orderId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getOrderFieldValue($orderId, "total_paid");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getTotalItemCount($orderId = 0)
    {
        $count = 0;
        try {
            $count = (int) $this->getOrderFieldValue($orderId, "total_item_count");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $count = 0;
        } finally {
            return $count;
        }
    }

    public function getTotalQtyOrdered($orderId = 0)
    {
        $count = 0;
        try {
            $count = (int) $this->getOrderFieldValue($orderId, "total_qty_ordered");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $count = 0;
        } finally {
            return $count;
        }
    }

    public function getCustomerIsGuest($orderId = 0)
    {
        $isGuest = 0;
        try {
            $isGuest = (int) $this->getOrderFieldValue($orderId, "customer_is_guest");
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isGuestOrder = false;
        } finally {
            return $isGuestOrder;
        }
    }

    public function getOrderCustomerName($order = null)
    {
        $name = null;
        try {
            $name = $this->helperData->getNameCase($this->helperAddress->getOrderCustomerName($order));
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getOrderCustomerEmail($order = null)
    {
        $email = null;
        try {
            $email = strtolower($this->helperAddress->getOrderCustomerEmail($order));
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $email = null;
        } finally {
            return $email;
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
        } catch (\Throwable $e) {
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
                $itemSku = $item->getSku();
                switch ($this->getArrayValue($items, $itemSku) !== null) {
                    case true:
                        $items[$itemSku]->setQtyInvoiced($items[$itemSku]->getQtyInvoiced() + $item->getQtyInvoiced());
                        break;

                    default:
                        $items[$itemSku] = $item;
                        break;
                }
            });
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $items = [];
        } finally {
            return $items;
        }
    }

    protected function getById($orderId = 0)
    {
        $order = null;
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
