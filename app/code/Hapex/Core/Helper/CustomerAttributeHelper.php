<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class CustomerAttributeHelper extends AttributeHelper
{
    protected $tableCustomer;
    protected $tableCustomerOption;
    protected $attributeTypeId;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->attributeTypeId = 1;
        $this->tableCustomer = $this->getSqlTableName("customer_entity");
        $this->tableCustomerOption = $this->getSqlTableName("eav_attribute_option_value");
    }

    public function getCustomerAttributeValue($customerId, $attributeCode)
    {
        $value = null;
        $attributeId = $this->getCustomerAttributeId($attributeCode);
        try {
            $tableName = $this->getCustomerAttributeTable($attributeId);
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $customerId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getCustomerEntityAttributeValue($customerId = 0, $fieldName = null)
    {
        $value = null;
        try {
            $sql  = "SELECT $fieldName FROM " . $this->tableCustomer ." WHERE entity_id = $customerId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getCustomerAttributeSelect($customerId = 0, $attributeCode = null)
    {
        return $this->getAttributeOptionValue((int)$this->getCustomerAttributeValue($customerId, $attributeCode));
    }

    public function getCustomerAttributeId($attributeCode)
    {
        return $this->getAttributeId($attributeCode, $this->attributeTypeId);
    }

    public function getCustomerAttributeTable($attributeId)
    {
        return $this->getAttributeTable($this->tableCustomer, $attributeId, $this->attributeTypeId);
    }
}
