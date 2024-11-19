<?php

namespace Hapex\Core\Helper;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\App\Helper\Context;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\ObjectManagerInterface;

class OrderAddressHelper extends BaseHelper
{
    protected $tableOrderAddress;
    protected $address;
    protected $countryFactory;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, Address $address)
    {
        parent::__construct($context, $objectManager);
        $this->address = $address;
        $this->tableOrderAddress = $this->helperDb->getSqlTableName('sales_order_address');
        $this->countryFactory = $this->generateClassObject(CountryFactory::class)->create();
    }

    public function getOrderBillingAddress($order = null)
    {
        $address = $this->address;
        try {
            $address = $order->getBillingAddress();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $address = $this->address;
        } finally {
            return $address;
        }
    }

    public function getOrderIdCustomerName($orderId = 0)
    {
        $customerName = null;
        try {
            $customerName = $this->getOrderIdShippingName($orderId);
            $billingName = $this->getOrderIdBillingName($orderId);
            $customerName = isset($customerName) && !empty(trim($customerName)) && strpos(trim($customerName), ' ') !== false ? trim($customerName) : (isset($billingName) && !empty(trim($billingName)) && strpos(trim($billingName), ' ') !== false ? trim($billingName) : null);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    public function getOrderIdShippingName($orderId = 0)
    {
        $name = null;
        try {
            $firstName = $this->getOrderAddressFieldValue($orderId, "firstname", "shipping");
            $lastName = $this->getOrderAddressFieldValue($orderId, "lastname", "shipping");
            if (!empty($firstName) && !empty($lastName))
                $name = "$firstName $lastName";
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getOrderIdBillingName($orderId = 0)
    {
        $name = null;
        try {
            $firstName = $this->getOrderAddressFieldValue($orderId, "firstname", "billing");
            $lastName = $this->getOrderAddressFieldValue($orderId, "lastname", "billing");
            if (!empty($firstName) && !empty($lastName))
                $name = "$firstName $lastName";
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getOrderShippingAddress($order = null)
    {
        $address = $this->address;
        try {
            $address = $order->getShippingAddress();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $address = $this->address;
        } finally {
            return $address;
        }
    }

    public function getOrderCustomerName($order = null)
    {
        $customerName = null;
        try {
            $address = $this->getOrderShippingAddress($order);
            $customerName = isset($address) && !empty(trim($address->getName())) && strpos(trim($address->getName()), ' ') !== false ? trim($address->getName()) : trim($this->getOrderBillingAddress($order)->getName());
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    public function getOrderCustomerEmail($order = null)
    {
        $customerEmail = null;
        try {
            $customerEmail = $this->getOrderBillingAddress($order)->getEmail();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $customerEmail = null;
        } finally {
            return $customerEmail;
        }
    }

    public function getAddressInfo($address = null)
    {
        $info = null;
        try {
            if (isset($address)) {
                $info = [];
                $info["name"] = $address->getName();
                $info["street"] = $this->getAddressStreet($address);
                $info["city"] = $address->getCity();
                $info["region"] = $address->getRegion();
                $info["postCode"] = $address->getPostcode();
                $info["country"] = $this->getCountry($address->getCountryId());
            }
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $info = null;
        } finally {
            return $info;
        }
    }

    private function getCountry($countryId = 0)
    {
        $name = null;
        try {
            if (isset($countryId)) {
                $country = $this->countryFactory->loadByCode($countryId);
                $name = $country->getName();
            }
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $name = null;
        } finally {
            return $name;
        }
    }

    private function getAddressStreet($address)
    {
        $street = $address->getStreetLine(1);
        if (empty($street)) {
            $street = $this->getStreet($address->getStreet());
        }
        return $street;
    }

    private function getStreet($data = [])
    {
        return $this->getArrayValue($data, 0);
    }

    private function getOrderAddressFieldValue($orderId = 0, $fieldName = null, $type = null)
    {
        try {
            $sql = "SELECT $fieldName FROM " . $this->tableOrderAddress . " where parent_id = $orderId";
            if (isset($type))
                $sql .= " AND address_type = '$type'";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $result = null;
        } finally {
            return $result;
        }
    }
}
