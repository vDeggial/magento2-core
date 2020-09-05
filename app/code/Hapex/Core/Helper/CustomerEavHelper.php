<?php

namespace Hapex\Core\Helper;


class CustomerEavHelper extends EavHelper
{

    public function getCustomerAttributeValue($customerId = 0, $attributeCode)
    {
        return $this->getAttributeValue("customer", $attributeCode, $customerId);
    }

    public function getCustomerEntityFieldValue($customerId = 0, $fieldName = null)
    {
        return $this->getEntityFieldValue("customer", $fieldName, $customerId);
    }

    public function getCustomerAttributeSelect($customerId = 0, $attributeCode = null)
    {
        return $this->getAttributeOptionValue((int) $this->getCustomerAttributeValue($customerId, $attributeCode));
    }
}
