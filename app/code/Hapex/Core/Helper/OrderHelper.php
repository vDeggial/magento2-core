<?php

namespace Hapex\Core\Helper;

use Magento\Sales\Model\OrderRepository;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class OrderHelper extends BaseHelper
{
    protected $tableOrder;
    protected $tableOrderItem;
    protected $tableOrderPayment;
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
        OrderRepository $orderRepository,
    ) {
        parent::__construct($context, $objectManager);
        $this->helperItem = $helperItem;
        $this->helperGrid = $helperGrid;
        $this->helperAddress = $helperAddress;
        $this->orderRepository = $orderRepository;
        $this->tableOrder = $this->helperDb->getSqlTableName('sales_order');
        $this->tableOrderItem = $this->helperDb->getSqlTableName('sales_order_item');
        $this->tableOrderPayment = $this->helperDb->getSqlTableName('sales_order_payment');
    }

    public function getOrder($orderId)
    {
        return $this->getById($orderId);
    }

    public function getOrderRow($orderId = 0)
    {
        $result = null;
        try {
            $sql = "SELECT * FROM " . $this->tableOrder . " WHERE entity_id = $orderId";
            $result = $this->helperDb->sqlQueryFetchRow($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }

    public function getOrderRowsWithSku($sku = null, $select = "*")
    {
        $result = [];
        try {
            $sql = "SELECT $select FROM " . $this->tableOrder . " WHERE entity_id IN(SELECT DISTINCT order_id FROM " . $this->tableOrderItem . " WHERE sku LIKE '$sku')";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = [];
        } finally {
            return $result;
        }
    }

    public function getOrdersByCustomerId($customerId = 0, $select = "*")
    {
        $result = [];
        try {
            $sql = "SELECT $select FROM " . $this->tableOrder . " WHERE customer_id = $customerId GROUP BY entity_id ORDER BY created_at DESC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = [];
        } finally {
            return $result;
        }
    }

    public function getOrderIdsByCustomerId($customerId = 0)
    {
        return $this->getOrdersByCustomerId($customerId, "entity_id");
    }

    public function getOrderIdsCreatedBetweenDates($dateFrom = "2022-07-01", $dateTo = null)
    {
        $dateFrom = isset($dateFrom) ? "'$dateFrom'" : "2022-07-01";
        $dateTo = isset($dateTo) ? "'$dateTo'" : "NOW()";
        $result = [];
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE created_at >= $dateFrom and created_at <= $dateTo ORDER BY created_at ASC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = [];
        } finally {
            return $result;
        }
    }

    public function getOrderIdsUpdatedBetweenDates($dateFrom = "2022-07-01", $dateTo = null)
    {
        $dateFrom = isset($dateFrom) ? "'$dateFrom'" : "2022-07-01";
        $dateTo = isset($dateTo) ? "'$dateTo'" : "NOW()";
        $result = [];
        try {
            $sql = "SELECT entity_id FROM " . $this->tableOrder . " WHERE updated_at >= $dateFrom and updated_at <= $dateTo ORDER BY updated_at ASC";
            $result = array_column($this->helperDb->sqlQueryFetchAll($sql), "entity_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $ruleIds = null;
        } finally {
            return $ruleIds;
        }
    }

    public function getCreatedDate($orderId = 0)
    {
        $date = null;
        try {
            $date = (string) $this->getOrderFieldValue($orderId, "created_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getUpdatedDate($orderId = 0)
    {
        $date = null;
        try {
            $date = (string) $this->getOrderFieldValue($orderId, "updated_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $incrementId = null;
        } finally {
            return $incrementId;
        }
    }

    public function getCustomerFirstName($orderId = 0)
    {
        $firstName = null;
        try {
            $firstName = $this->getOrderFieldValue($orderId, "customer_firstname");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $firstName = null;
        } finally {
            return $firstName;
        }
    }

    public function getCustomerLastName($orderId = 0)
    {
        $lastName = null;
        try {
            $lastName = $this->getOrderFieldValue($orderId, "customer_lastname");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $lastName = null;
        } finally {
            return $lastName;
        }
    }

    public function getCustomerFullName($orderId = 0)
    {
        $firstName = null;
        $lastName = null;
        $fullName = null;
        try {
            $firstName = $this->getCustomerFirstName($orderId);
            $lastName = $this->getCustomerLastName($orderId);
            $fullName = "$firstName $lastName";
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $fullName = null;
        } finally {
            return $fullName;
        }
    }

    public function getRemoteIP($orderId = 0)
    {
        $ip = null;
        try {
            $ip = $this->getOrderFieldValue($orderId, "remote_ip");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $ip = null;
        } finally {
            return $ip;
        }
    }

    public function getOrderCustomerId($order = null)
    {
        $customerId = 0;
        try {
            $customerId = $order->getCustomer()->getId();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $customerId = 0;
        } finally {
            return $customerId;
        }
    }

    public function getOrderCustomerGroupId($order = null)
    {
        $groupId = 0;
        try {
            $groupId = $order->getCustomer()->getGroupId();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $groupId = 0;
        } finally {
            return $groupId;
        }
    }

    public function getOrderPaymentMethod($order)
    {
        $paymentMethod = null;
        try {
            $payment = $order->getPayment();
            $paymentInstance = $payment->getMethodInstance();
            $paymentMethod = $paymentInstance->getTitle();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $paymentMethod = null;
        } finally {
            return $paymentMethod;
        }
    }

    public function getPaymentMethod($orderId = 0)
    {
        $paymentMethod = null;
        try {
            $order = $this->getById($orderId);
            switch (isset($order)) {
                case true:
                    $paymentMethod = $this->getOrderPaymentMethod($order);
                    break;
            }
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $paymentMethod = null;
        } finally {
            return $paymentMethod;
        }
    }

    public function getIsVirtual($orderId = 0)
    {
        $isVirtual = 0;
        try {
            $isVirtual = (int) $this->getOrderFieldValue($orderId, "is_virtual");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getShippingMethod($orderId = 0)
    {
        $method = null;
        try {
            $method = $this->getOrderFieldValue($orderId, "shipping_method");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $isGuestOrder = false;
        } finally {
            return $isGuestOrder;
        }
    }

    public function getOrderCustomerName($order = null)
    {
        $name = null;
        try {
            $helperData = $this->generateClassObject(\Hapex\Core\Helper\DataHelper::class);
            $name = $helperData->getNameCase($this->helperAddress->getOrderCustomerName($order));
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $items = [];
        } finally {
            return $items;
        }
    }

    protected function getOrderItemsMergeSku($order = null)
    {
        $orderItems = $this->getOrderItems($order);
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }
}
