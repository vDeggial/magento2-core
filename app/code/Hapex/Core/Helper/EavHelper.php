<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

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

    public function getAttributeId($attributeCode = null, $attributeType = null)
    {
        $attributeId = 0;
        try {
            $attributeTypeId = $this->getEntityTypeId($attributeType);
            $sql = "SELECT attribute_id from " . $this->tableAttribute . " WHERE entity_type_id = $attributeTypeId AND attribute_code LIKE '$attributeCode'";
            $result = (int) $this->sqlQueryFetchOne($sql);
            $attributeId = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $attributeId = 0;
        } finally {
            return $attributeId;
        }
    }

    public function getAttributeSetId($setName = null, $attributeType = null)
    {
        $attributeSetId = 0;
        try {
            $attributeTypeId = $this->getEntityTypeId($attributeType);
            $sql = "SELECT attribute_set_id from " . $this->tableAttributeSet . " WHERE entity_type_id = $attributeTypeId AND attribute_set_name LIKE '$setName'";
            $result = (int) $this->sqlQueryFetchOne($sql);
            $attributeSetId = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $attributeSetId = 0;
        } finally {
            return $attributeSetId;
        }
    }

    public function getAttributeTable($attributeType = null, $backendType = null)
    {
        $tableName = null;
        try {
            $tableName = $this->getSqlTableName($this->getEntityTable($attributeType));
            if ($backendType !== null) {
                $format = "%s_%s";
                $tableName = sprintf($format, $tableName, $backendType);
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $tableName = null;
        } finally {
            return $tableName;
        }
    }

    public function getAttributeBackendType($attributeId = 0, $attributeType = null)
    {
        $backendType = null;
        try {
            $attributeTypeId = $this->getEntityTypeId($attributeType);
            $sql = "SELECT backend_type from " . $this->tableAttribute . " WHERE entity_type_id = $attributeTypeId AND attribute_id = $attributeId";
            $result = (string) $this->sqlQueryFetchOne($sql);
            $backendType = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $backendType = null;
        } finally {
            return $backendType;
        }
    }

    public function getAttributeValue($attributeType = null, $attributeCode = null, $entityId = 0)
    {
        $value = null;
        try {
            $attributeId = $this->getAttributeId($attributeCode, $attributeType);
            $backendType = $attributeId > 0 ? $this->getAttributeBackendType($attributeId, $attributeType) : null;
            switch ($attributeId > 0 && $backendType !== "static") {
                case true:
                    $value = $this->getValue($entityId, $attributeType, $attributeId, $backendType);
                    break;

                default:
                    $value = $this->getEntityFieldValue($attributeType, $attributeCode, $entityId);
                    break;
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getEntityFieldValue($attributeType = null, $fieldName = null, $entityId = 0)
    {
        $value = null;
        try {
            $tableName = $this->getSqlTableName($this->getEntityTable($attributeType));
            $sql = "SELECT $fieldName FROM $tableName WHERE entity_id = $entityId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getAttributeOptionValue($optionId = 0)
    {
        $optionValue = null;
        try {
            $sql = "SELECT value from " . $this->tableAttributeOption . " WHERE option_id = $optionId";
            $result = (string) $this->sqlQueryFetchOne($sql);
            $optionValue = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $optionValue = null;
        } finally {
            return $optionValue;
        }
    }

    protected function getEntityTypeId($typeCode = null)
    {
        $typeId = 0;
        try {
            $sql = "SELECT entity_type_id FROM " . $this->tableEntityType . " WHERE entity_type_code LIKE '$typeCode'";
            $result = (int) $this->sqlQueryFetchOne($sql);
            $typeId = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $typeId = 0;
        } finally {
            return $typeId;
        }
    }

    protected function getEntityTypeCode($typeId = 0)
    {
        $typeCode = null;
        try {
            $sql = "SELECT entity_type_code FROM " . $this->tableEntityType . " WHERE entity_type_id = $typeId";
            $result = (string) $this->sqlQueryFetchOne($sql);
            $typeCode = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $typeCode = null;
        } finally {
            return $typeCode;
        }
    }

    protected function getEntityTable($type = null)
    {
        $entityTable = null;
        try {
            $typeId = $this->getEntityTypeId($type);
            $sql = "SELECT entity_table FROM " . $this->tableEntityType . " WHERE entity_type_id = $typeId";
            $result = (string) $this->sqlQueryFetchOne($sql);
            $entityTable = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $entityTable = null;
        } finally {
            return $entityTable;
        }
    }

    private function getValue($entityId = 0, $attributeType = null, $attributeId = 0, $backendType = null)
    {
        $value = null;
        try {
            $tableName = $this->getAttributeTable($attributeType, $backendType);
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
