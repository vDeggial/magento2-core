<?php

namespace Hapex\Core\Helper;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CustomerHelper extends BaseHelper
{
    protected $session;
    protected $customerFactory;
    protected $customerRepository;
    protected $helperEav;
    protected $tableCustomer;
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CustomerEavHelper $helperEav,
        SessionFactory $sessionFactory,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $objectManager);
        $this->helperEav = $helperEav;
        $this->session = $sessionFactory->create();
        $this->customerFactory = $customerFactory->create();
        $this->customerRepository = $customerRepository;
        $this->tableCustomer = $this->helperDb->getSqlTableName('customer_entity');
    }

    public function getCustomer($customerId = 0)
    {
        return $this->getCustomerById($customerId);
    }

    public function getActiveCustomers($group = 0, $select = "*", $limit = 0, $offset = 0)
    {
        $where = "is_active = 1";
        if (!empty($group)) {
            $where .= " AND group_id IN($group)";
        }
        $sql = "SELECT $select FROM " . $this->tableCustomer . " WHERE $where";
        $result = $this->helperDb->sqlQueryFetchAll($sql);
        $customers = $result;
        return $customers;
    }

    public function getCustomerOrderedQuantity($customerId = 0, $productSku = null)
    {
        $quantity = 0;
        try {
            switch ($this->customerExists($customerId)) {
                case true:
                    $tableOrders = $this->helperDb->getSqlTableName('sales_order');
                    $tableItems = $this->helperDb->getSqlTableName('sales_order_item');
                    $sql = "SELECT * FROM `$tableOrders` orders join `$tableItems` items on orders.entity_id = items.order_id where orders.customer_id = $customerId and items.sku = '$productSku'";
                    $result = $this->helperDb->sqlQueryFetchAll($sql);
                    if ($result) {
                        $qty_ordered = array_sum(array_column($result, "qty_ordered"));
                        $qty_refunded = array_sum(array_column($result, "qty_refunded"));
                        $qty_canceled = array_sum(array_column($result, "qty_canceled"));

                        $qty = $qty_ordered - $qty_refunded - $qty_canceled;
                        $quantity = $qty;
                    }
                    break;
            }
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $quantity = 0;
        } finally {
            return $quantity;
        }
    }

    public function customerExists($customerId = 0)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableCustomer . " customer where customer.entity_id = $customerId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $customerDob = null;
        } finally {
            return $customerDob;
        }
    }

    public function getCustomerCreatedDate($customerId = 0)
    {
        $date = null;
        try {
            $date = $this->helperEav->getCustomerEntityFieldValue($customerId, "created_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getCustomerUpdatedDate($customerId = 0)
    {
        $date = null;
        try {
            $date = $this->helperEav->getCustomerEntityFieldValue($customerId, "updated_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $date = null;
        } finally {
            return $date;
        }
    }

    public function getCustomerEmail($customerId = 0)
    {
        $customerEmail = null;
        try {
            $customerEmail = $this->helperEav->getCustomerEntityFieldValue($customerId, "email");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
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
        return $this->customerRepository->getById($customerId);
    }
}
