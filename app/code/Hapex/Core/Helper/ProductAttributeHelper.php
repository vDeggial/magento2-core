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
        return $this->getAttributeValue($this->tableProduct, $this->attributeTypeId, $attributeCode, $productId);
    }

    public function getProductEntityAttributeValue($productId = 0, $fieldName = null)
    {
        return $this->getEntityAttributeValue($this->tableProduct, $fieldName, $productId);
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
