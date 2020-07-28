<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class CustomerAttributeHelper extends AttributeHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
    }

    public function getCustomerAttributeValue($customerId, $attributeCode)
    {
        return $this->getAttributeValue("customer", $attributeCode, $customerId);
    }

    public function getCustomerEntityAttributeValue($customerId = 0, $fieldName = null)
    {
        return $this->getEntityAttributeValue("customer", $fieldName, $customerId);
    }

    public function getCustomerAttributeSelect($customerId = 0, $attributeCode = null)
    {
        return $this->getAttributeOptionValue((int)$this->getCustomerAttributeValue($customerId, $attributeCode));
    }
}
