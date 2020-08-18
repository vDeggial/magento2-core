<?php

namespace Hapex\Core\Helper;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class OrderAddressHelper extends BaseHelper
{
    protected $tableOrderAddress;
    protected $address;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, Address $address)
    {
        parent::__construct($context, $objectManager);
        $this->address = $address;
        $this->tableOrderAddress = $this->helperDb->getSqlTableName('sales_order_address');
    }

    public function getOrderBillingAddress($order = null)
    {
        $address = $this->address;
        try {
            $address = $order->getBillingAddress();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $address = $this->address;
        } finally {
            return $address;
        }
    }

    public function getOrderShippingAddress($order = null)
    {
        $address = $this->address;
        try {
            $address = $order->getShippingAddress();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $address = $this->address;
        } finally {
            return $address;
        }
    }

    public function getOrderCustomerName($order = null)
    {
        $customerName = null;
        try {
            $address = $this->getOrderBillingAddress($order);
            $customerName = $address->getName();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    public function getOrderCustomerEmail($order = null)
    {
        $customerEmail = null;
        try {
            $address = $this->getOrderBillingAddress($order);
            $customerEmail = $address->getEmail();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerEmail = null;
        } finally {
            return $customerEmail;
        }
    }
}
