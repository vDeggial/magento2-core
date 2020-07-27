<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class CustomerAttributeHelper extends AttributeHelper
{
    protected $tableCustomer;
    protected $attributeTypeId;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->attributeTypeId = 1;
        $this->tableCustomer = $this->getSqlTableName("customer_entity");
    }

    public function getCustomerAttributeValue($customerId, $attributeCode)
    {
        return $this->getAttributeValue($this->tableCustomer, $this->attributeTypeId, $attributeCode, $customerId);
    }

    public function getCustomerEntityAttributeValue($customerId = 0, $fieldName = null)
    {
        return $this->getEntityAttributeValue($this->tableCustomer, $fieldName, $customerId);
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
