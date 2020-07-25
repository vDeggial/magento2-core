<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class ProductAttributeHelper extends AttributeHelper
{
    protected $tableProduct;
    protected $tableProductOption;
    protected $attributeTypeId;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->attributeTypeId = 4;
        $this->tableProduct = $this->getSqlTableName('catalog_product_entity');
        $this->tableProductOption = $this->getSqlTableName("eav_attribute_option_value");
    }

    public function getProductAttributeValue($productId, $attributeCode)
    {
        $value = null;
        $attributeId = $this->getProductAttributeId($attributeCode);
        try {
            $tableName = $this->getProductAttributeTable($attributeId);
            $sql = "SELECT value FROM $tableName WHERE attribute_id = $attributeId AND entity_id = $productId";
            //$this->helperLog->errorLog(__METHOD__, $sql);
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getProductEntityAttributeValue($productId = 0, $fieldName = null)
    {
        $value = null;
        try {
            $sql  = "SELECT $fieldName FROM " . $this->tableProduct ." WHERE entity_id = $productId";
            $result = $this->sqlQueryFetchOne($sql);
            $value = $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getProductAttributeSelect($productId = 0, $attributeCode = null)
    {
        return $this->getAttributeOptionValue((int)$this->getProductAttributeValue($productId, $attributeCode));
    }

    public function getProductAttributeId($attributeCode)
    {
        return $this->getAttributeId($attributeCode, $this->attributeTypeId);
    }

    public function getProductAttributeTable($attributeId)
    {
        return $this->getAttributeTable($this->tableProduct, $attributeId, $this->attributeTypeId);
    }
}
