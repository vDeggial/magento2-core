<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class OrderGridHelper extends BaseHelper
{
    protected $tableOrderGrid;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableOrderGrid = $this->helperDb->getSqlTableName('sales_order_grid');
    }

    public function getBillingName($orderId = 0)
    {
        $fullName = null;
        try {
            $fullName = $this->getOrderGridFieldValue($orderId, "billing_name");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $fullName = null;
        } finally {
            return $fullName;
        }
    }

    public function getOrderName($orderId = 0)
    {
        $fullName = null;
        try {
            $fullName = trim($this->getShippingName($orderId));
            if (empty($fullName) || strpos($fullName, ' ') === false) $fullName = $this->getBillingName($orderId);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $fullName = null;
        } finally {
            return $fullName;
        }
    }

    public function getCustomerEmail($orderId = 0)
    {
        $email = null;
        try {
            $email = $this->getOrderGridFieldValue($orderId, "customer_email");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $email = null;
        } finally {
            return $email;
        }
    }

    public function getShippingName($orderId = 0)
    {
        $fullName = null;
        try {
            $fullName = $this->getOrderGridFieldValue($orderId, "shipping_name");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $fullName = null;
        } finally {
            return $fullName;
        }
    }

    private function getOrderGridFieldValue($orderId = 0, $fieldName = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableOrderGrid . " where entity_id = $orderId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $result = null;
        } finally {
            return $result;
        }
    }
}
