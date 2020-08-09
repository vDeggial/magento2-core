<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class CustomerEavHelper extends EavHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager, ResourceConnection $resource, LogHelper $helperLog)
    {
        parent::__construct($context, $objectManager, $resource, $helperLog);
    }

    public function getCustomerAttributeValue($customerId, $attributeCode)
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
