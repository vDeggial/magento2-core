<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class ProductEavHelper extends EavHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
    }

    public function getProductAttributeValue($productId, $attributeCode)
    {
        return $this->getAttributeValue("catalog_product", $attributeCode, $productId);
    }

    public function getProductEntityFieldValue($productId = 0, $fieldName = null)
    {
        return $this->getEntityFieldValue("catalog_product", $fieldName, $productId);
    }

    public function getProductAttributeSelect($productId = 0, $attributeCode = null)
    {
        return $this->getAttributeOptionValue((int) $this->getProductAttributeValue($productId, $attributeCode));
    }
}
