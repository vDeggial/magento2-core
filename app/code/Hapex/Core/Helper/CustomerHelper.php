<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class CustomerHelper extends BaseHelper
{
    protected $session;
    protected $tableCustomer;
    protected $tableAttribute;
    protected $attributeTypeId;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->attributeTypeId = 1;
        $this->session = $this->generateClassObject('Magento\Customer\Model\Session');
        $this->tableCustomer = $this->getSqlTableName("customer_entity");
        $this->tableAttribute = $this->getSqlTableName("eav_attribute");
    }

    public function getCustomer($customerId = 0)
    {
        return $this->getCustomerById($customerId);
    }

    public function getAttributeValue($customerId = null, $attribute = null)
    {
        return $this->getCustomerAttributeValue($customerId, $attribute);
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
        $factory = $this->generateClassObject("Magento\Customer\Model\CustomerFactory")->create();
        return $factory->load($customerId);
    }

    private function getCustomerGroup($customerId)
    {
      $customerGroup = 0;
      try {
          $sql  = "SELECT group_id FROM " . $this->tableCustomer ." WHERE entity_id = $customerId";
          $result = $this->sqlQueryFetchOne($sql);
          $customerGroup = (int)$result;
      } catch (\Exception $e) {
          $this->errorLog(__METHOD__, $e->getMessage());
          $customerGroup = 0;
      } finally {
          return $customerGroup;
      }
    }

    private function getCustomerAttributeId($attributeCode)
    {
        $attributeId = 0;
        try {
            $sql = "SELECT attribute_id from " . $this->tableAttribute . " WHERE entity_type_id = " . $this->attributeTypeId . " AND attribute_code LIKE '$attributeCode'";
            $result = (int)$this->sqlQueryFetchOne($sql);
            $attributeId = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $attributeId = 0;
        } finally {
            return $attributeId;
        }
    }

    private function getCustomerAttributeTable($attributeId)
    {
        $tableName = $this->tableCustomer;
        try {
            $attributeType = $this->getCustomerAttributeType($attributeId);
            $tableName .= "_" . $attributeType;
            $tableName = $this->getSqlTableName($tableName);
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $tableName = $this->tableCustomer;
        } finally {
            return $tableName;
        }
    }

    private function getCustomerAttributeType($attributeId)
    {
        $attributeType = null;
        try {
            $sql = "SELECT backend_type from " . $this->tableAttribute . " WHERE entity_type_id = " . $this->attributeTypeId . " AND attribute_id = $attributeId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $attributeType = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $attributeType = null;
        } finally {
            return $attributeType;
        }
    }

    private function getCustomerAttributeValue($customerId, $attributeCode)
    {
        $value = null;
        $attributeId = $this->getCustomerAttributeId($attributeCode);
        try {
            $tableName = $this->getCustomerAttributeTable($attributeId);
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $customerId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }
}
