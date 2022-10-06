<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\CreditmemoRepository;

class CreditMemoHelper extends BaseHelper
{
    protected $tableCreditMemo;
    protected $helperItem;
    protected $helperGrid;
    protected $helperComment;
    protected $helperOrder;
    protected $helperOrderGrid;
    protected $repository;
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CreditMemoItemHelper $helperItem,
        CreditMemoGridHelper $helperGrid,
        CreditMemoCommentHelper $helperComment,
        OrderHelper $helperOrder,
        OrderGridHelper $helperOrderGrid,
        CreditmemoRepository $repository
    ) {
        parent::__construct($context, $objectManager);
        $this->tableCreditMemo = $this->helperDb->getSqlTableName("sales_creditmemo");
        $this->helperItem = $helperItem;
        $this->helperGrid = $helperGrid;
        $this->helperComment = $helperComment;
        $this->helperOrder = $helperOrder;
        $this->helperOrderGrid = $helperOrderGrid;
        $this->repository = $repository;
    }

    public function getMemo($memoId = 0)
    {
        return $this->getById($memoId);
    }

    public function getCreatedDate($memoId = 0)
    {
        $date = null;
        try {
            $date =  $this->getMemoFieldValue($memoId, "created_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getUpdatedDate($memoId = 0)
    {
        $date = null;
        try {
            $date =  $this->getMemoFieldValue($memoId, "updated_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getCustomerName($memoId = 0)
    {
        $name  = null;
        try {
            $orderId = $this->getOrderId($memoId);
            $name = $this->helperOrderGrid->getBillingName($orderId);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getCustomerEmail($memoId = 0)
    {
        $email  = null;
        try {
            $orderId = $this->getOrderId($memoId);
            $email = $this->helperOrderGrid->getCustomerEmail($orderId);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $email = null;
        } finally {
            return $email;
        }
    }

    public function getCustomerNote($memoId = 0)
    {
        $note = null;
        try {
            $note = $this->getMemoFieldValue($memoId, "customer_note");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $note = null;
        } finally {
            return $note;
        }
    }

    public function getDiscountAmount($memoId = 0)
    {
        $amount = 0;
        try {
            $amount = $this->getMemoFieldValue($memoId, "discount_amount");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getDiscountDescription($memoId = 0)
    {
        $description = null;
        try {
            $description = $this->getMemoFieldValue($memoId, "discount_description");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getGrandTotal($memoId = 0)
    {
        $total = 0;
        try {
            $total = $this->getMemoFieldValue($memoId, "grand_total");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getIncrementId($memoId = 0)
    {
        $incrementId = null;
        try {
            $incrementId = $this->getMemoFieldValue($memoId, "increment_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $incrementId = null;
        } finally {
            return $incrementId;
        }
    }

    public function getOrderId($memoId = 0)
    {
        $orderId = 0;
        try {
            $orderId = $this->getMemoFieldValue($memoId, "order_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $orderId = 0;
        } finally {
            return $orderId;
        }
    }

    public function getOrderIncrementId($memoId = 0)
    {
        $incrementId = 0;
        try {
            $orderid = $this->getOrderId($memoId);
            $incrementId = $this->helperOrder->getIncrementId($orderid);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $incrementId = 0;
        } finally {
            return $incrementId;
        }
    }

    public function getState($memoId = 0)
    {
        $state = 0;
        try {
            $state = $this->getMemoFieldValue($memoId, "state");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $state = 0;
        } finally {
            return $state;
        }
    }

    public function getStatus($memoId = 0)
    {
        $status = 0;
        try {
            $status = $this->getMemoFieldValue($memoId, "creditmemo_status");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getSubTotal($memoId = 0)
    {
        $total = 0;
        try {
            $total = $this->getMemoFieldValue($memoId, "subtotal");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function memoExists($memoId = 0)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableCreditMemo . " memo where memo.entity_id = $memoId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    public function getMemoAdjustment($memo = null)
    {
        $adjustment = 0;
        try {
            $adjustment = $memo->getAdjustment();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $adjustment = 0;
        } finally {
            return $adjustment;
        }
    }

    public function getMemoCustomerName($memo = null)
    {
        $name = null;
        try {
            $name = $memo->getOrder()->getBillingAddress()->getName();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getMemoCustomerEmail($memo = null)
    {
        $email = null;
        try {
            $email = $memo->getOrder()->getBillingAddress()->getEmail();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $email = null;
        } finally {
            return $email;
        }
    }

    public function getMemoDiscountAmount($memo = null)
    {
        $amount = 0;
        try {
            $amount = $memo->getDiscountAmount();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }

    public function getMemoGrandTotal($memo = null)
    {
        $total = 0;
        try {
            $total = $memo->getGrandTotal();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getMemoOrder($memo = null)
    {
        $order = null;
        try {
            $order = $memo->getOrder();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $order = null;
        } finally {
            return $order;
        }
    }

    public function getMemoOrderIncrementId($memo = null)
    {
        $incrementId = 0;
        try {
            $order = $this->getMemoOrder($memo);
            $incrementId = isset($order) ? $order->getIncrementId() : 0;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $incrementId = 0;
        } finally {
            return $incrementId;
        }
    }

    public function getMemoSubtotal($memo = null)
    {
        $total = 0;
        try {
            $total = $memo->getSubtotal();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $total = 0;
        } finally {
            return $total;
        }
    }

    public function getMemoTaxAmount($memo = null)
    {
        $amount = 0;
        try {
            $amount = $memo->getTaxAmount();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $amount = 0;
        } finally {
            return $amount;
        }
    }


    protected function getById($memoId = 0)
    {
        $memo = null;
        try {
            $memo = $this->memoExists($memoId) ? $this->repository->get($memoId) : null;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $memo = null;
        } finally {
            return $memo;
        }
    }

    protected function getMemoFieldValue($memoId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableCreditMemo . " where entity_id = $memoId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
