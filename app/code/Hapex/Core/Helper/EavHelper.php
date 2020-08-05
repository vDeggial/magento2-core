<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class EavHelper extends DbHelper
{
    protected $tableAttribute;
    protected $tableAttributeSet;
    protected $tableAttributeOption;
    protected $tableEntityType;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableAttribute = $this->getSqlTableName("eav_attribute");
        $this->tableAttributeSet = $this->getSqlTableName("eav_attribute_set");
        $this->tableEntityType = $this->getSqlTableName("eav_entity_type");
        $this->tableAttributeOption = $this->getSqlTableName("eav_attribute_option_value");
    }

    public function getAttributeId($attributeCode, $attributeTypeId)
    {
        $attributeId = 0;
        try {
            $sql = "SELECT attribute_id from " . $this->tableAttribute . " WHERE entity_type_id = $attributeTypeId AND attribute_code LIKE '$attributeCode'";
            $result = (int)$this->sqlQueryFetchOne($sql);
            $attributeId = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $attributeId = 0;
        } finally {
            return $attributeId;
        }
    }

    public function getAttributeSetId($setName = "", $attributeTypeId)
    {
        $attributeSetId = 0;
        try {
            $sql = "SELECT attribute_set_id from " . $this->tableAttributeSet . " WHERE entity_type_id = $attributeTypeId AND attribute_set_name LIKE '$setName'";
            $result = (int)$this->sqlQueryFetchOne($sql);
            $attributeSetId = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $attributeSetId = 0;
        } finally {
            return $attributeSetId;
        }
    }

    public function getAttributeTable($attributeTypeId, $backendType)
    {
        $tableName = null;
        try {
            $tableName = $this->getSqlTableName($this->getEntityTable($attributeTypeId));
            $format = "%s_%s";
            $tableName = sprintf($format, $tableName, $backendType);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $tableName = null;
        } finally {
            return $tableName;
        }
    }

    public function getAttributeBackendType($attributeId, $attributeTypeId)
    {
        $attributeType = null;
        try {
            $sql = "SELECT backend_type from " . $this->tableAttribute . " WHERE entity_type_id = $attributeTypeId AND attribute_id = $attributeId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $attributeType = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $attributeType = null;
        } finally {
            return $attributeType;
        }
    }

    public function getAttributeValue($attributeTypeId = 0, $attributeCode = null, $entityId = 0)
    {
        $value = null;
        try {
            $attributeId = $this->getAttributeId($attributeCode, $attributeTypeId);
            $backendType = $attributeId > 0 ? $this->getAttributeBackendType($attributeId, $attributeTypeId) : null;
            switch ($attributeId > 0 && $backendType !== "static") {
              case true:
              $value = $this->getValue($entityId, $attributeTypeId, $attributeId, $backendType);
              break;

              default:
              $value = $this->getEntityFieldValue($attributeTypeId, $attributeCode, $entityId);
              break;
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getEntityFieldValue($attributeTypeId = 0, $fieldName = null, $entityId = 0)
    {
        $value = null;
        try {
            $tableName = $this->getSqlTableName($this->getEntityTable($attributeTypeId));
            $sql  = "SELECT $fieldName FROM $tableName WHERE entity_id = $entityId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getAttributeOptionValue($optionId)
    {
        $optionValue = null;
        try {
            $sql = "SELECT value from " . $this->tableAttributeOption . " WHERE option_id = $optionId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $optionValue = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $optionValue = null;
        } finally {
            return $optionValue;
        }
    }

    protected function getAttributeTypeId($typeCode = "")
    {
        $typeId = 0;
        try {
            $sql = "SELECT entity_type_id FROM " . $this->tableEntityType . " WHERE entity_type_code LIKE '$typeCode'";
            $result = (int)$this->sqlQueryFetchOne($sql);
            $typeId = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $typeId = 0;
        } finally {
            return $typeId;
        }
    }

    protected function getAttributeTypeCode($typeId = 0)
    {
        $typeCode = null;
        try {
            $sql = "SELECT entity_type_code FROM " . $this->tableEntityType . " WHERE entity_type_id = $typeId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $typeCode = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $typeCode = null;
        } finally {
            return $typeCode;
        }
    }

    protected function getEntityTable($typeId = 0)
    {
        $entityTable = null;
        try {
            $sql = "SELECT entity_table FROM " . $this->tableEntityType . " WHERE entity_type_id = $typeId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $entityTable = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $entityTable = null;
        } finally {
            return $entityTable;
        }
    }

    private function getValue($entityId, $attributeTypeId, $attributeId, $backendType)
    {
        $value = null;
        try {
            $tableName = $this->getAttributeTable($attributeTypeId, $backendType);
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $entityId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }
}
