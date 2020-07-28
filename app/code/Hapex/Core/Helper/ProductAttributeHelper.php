<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class ProductAttributeHelper extends AttributeHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
    }

    public function getProductAttributeValue($productId, $attributeCode)
    {
        return $this->getAttributeValue($this->getAttributeTypeId("catalog_product"), $attributeCode, $productId);
    }

    public function getProductEntityAttributeValue($productId = 0, $fieldName = null)
    {
        return $this->getEntityAttributeValue($this->getAttributeTypeId("catalog_product"), $fieldName, $productId);
    }

    public function getProductAttributeSelect($productId = 0, $attributeCode = null)
    {
        return $this->getAttributeOptionValue((int)$this->getProductAttributeValue($productId, $attributeCode));
    }
}
