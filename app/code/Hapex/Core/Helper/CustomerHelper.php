<?php

namespace Hapex\Core\Helper;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CustomerHelper extends BaseHelper
{
    protected $session;
    protected $customerFactory;
    protected $helperEav;
    protected $tableCustomer;
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CustomerEavHelper $helperEav,
        SessionFactory $sessionFactory,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context, $objectManager);
        $this->helperEav = $helperEav;
        $this->session = $sessionFactory->create();
        $this->customerFactory = $customerFactory->create();
        $this->tableCustomer = $this->helperDb->getSqlTableName('customer_entity');
    }

    public function getCustomer($customerId = 0)
    {
        return $this->getCustomerById($customerId);
    }

    public function customerExists($customerId = 0)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableCustomer . " customer where customer.entity_id = $customerId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    public function getCustomerDob($customerId = 0)
    {
        $customerDob = null;
        try {
            $customerDob = $this->helperEav->getCustomerEntityFieldValue($customerId, "dob");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerDob = null;
        } finally {
            return $customerDob;
        }
    }

    public function getCustomerEmail($customerId = 0)
    {
        $customerEmail = null;
        try {
            $customerEmail = $this->helperEav->getCustomerEntityFieldValue($customerId, "email");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerEmail = null;
        } finally {
            return $customerEmail;
        }
    }

    public function getCustomerGroup($customerId = 0)
    {
        $customerGroup = 0;
        try {
            $customerGroup = (int) $this->helperEav->getCustomerEntityFieldValue($customerId, "group_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerGroup = 0;
        } finally {
            return $customerGroup;
        }
    }

    public function getCustomerStatus($customerId = 0)
    {
        $customerStatus = 0;
        try {
            $customerStatus = (int) $this->helperEav->getCustomerEntityFieldValue($customerId, "is_active");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerStatus = 0;
        } finally {
            return $customerStatus;
        }
    }

    public function getCustomerGender($customerId = 0)
    {
        $customerGender = 0;
        try {
            $customerGender = (int) $this->helperEav->getCustomerEntityFieldValue($customerId, "gender");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerGender = 0;
        } finally {
            return $customerGender;
        }
    }

    public function getCustomerFirstName($customerId = 0)
    {
        $customerName = null;
        try {
            $customerName = $this->helperEav->getCustomerEntityFieldValue($customerId, "firstname");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    public function getCustomerLastName($customerId = 0)
    {
        $customerName = null;
        try {
            $customerName = $this->helperEav->getCustomerEntityFieldValue($customerId, "lastname");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerName = null;
        } finally {
            return $customerName;
        }
    }

    public function getCustomerIdByEmail($customerEmail = null)
    {
        $customerId = 0;
        try {
            $sql = "SELECT entity_id FROM " . $this->tableCustomer . " WHERE email like '$customerEmail'";
            $customerId = (int) $this->helperDb->sqlQueryFetchOne($sql);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerId = 0;
        } finally {
            return $customerId;
        }
    }

    public function getAttributeValue($customerId = 0, $attribute = null)
    {
        return $this->helperEav->getCustomerAttributeValue($customerId, $attribute);
    }

    public function getLoggedInGroup()
    {
        return $this->getCustomerGroup($this->getLoggedInCustomerId());
    }

    public function isLoggedIn()
    {
        return $this->session && $this->session->isLoggedIn();
    }

    public function getLoggedInCustomer()
    {
        return $this->isLoggedIn() ? $this->getCustomer($this->getLoggedInCustomerId()) : null;
    }

    public function getLoggedInCustomerId()
    {
        return $this->isLoggedIn() ? $this->session->getCustomer()->getId() : 0;
    }

    private function getCustomerById($customerId = 0)
    {
        return $this->customerFactory->load($customerId);
    }
}
