<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class AttributeHelper extends DbHelper
{
    protected $tableAttribute;
    protected $tableAttributeSet;
    protected $tableAttributeOption;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableAttribute = $this->getSqlTableName("eav_attribute");
        $this->tableAttributeSet = $this->getSqlTableName("eav_attribute_set");
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

    public function getAttributeSetId($setName = "", $attributeTypeId = 1)
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

    public function getAttributeTable($table, $attributeId, $attributeTypeId)
    {
        $tableName = $table;
        try {
            $attributeType = $this->getAttributeType($attributeId, $attributeTypeId);
            $format = "%s_%s";
            $tableName = sprintf($format, $table, $attributeType);
            $tableName = $this->getSqlTableName($tableName);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $tableName = $table . "_";
        } finally {
            return $tableName;
        }
    }

    public function getAttributeType($attributeId, $attributeTypeId)
    {
        $attributeType = null;
        try {
            $sql = "SELECT backend_type from " . $this->tableAttribute . " WHERE entity_type_id = $attributeTypeId AND attribute_id = $attributeId";
            $result = (string)$this->sqlQueryFetchOne($sql);
            $attributeType = $result;
            $this->helperLog->errorLog(__METHOD__, $attributeType);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $attributeType = null;
        } finally {
            return $attributeType;
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
}
