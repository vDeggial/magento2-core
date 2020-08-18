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
            $customerName = $this->getOrderBillingAddress($order)->getName();
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
            $customerEmail = $this->getOrderBillingAddress($order)->getEmail();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
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
                $info["street"] = $this->getStreet($address->getStreet());
                $info["city"] = $address->getCity();
                $info["region"] = $address->getRegion();
                $info["postCode"] = $address->getPostcode();
                $info["country"] = $this->getCountry($address->getCountryId());
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
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
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    private function getStreet($data = [])
    {
        return isset($data[0]) ? $data[0] : null;
    }
}
