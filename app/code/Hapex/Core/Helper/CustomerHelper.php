<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class CustomerHelper extends BaseHelper
{
    protected $session;
    protected $helperAttribute;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->helperAttribute = $this->objectManager->get("Hapex\Core\Helper\CustomerAttributeHelper");
        $this->session = $this->generateClassObject('Magento\Customer\Model\Session');
    }

    public function getCustomer($customerId = 0)
    {
        return $this->getCustomerById($customerId);
    }

    public function getAttributeValue($customerId = null, $attribute = null)
    {
        return $this->helperAttribute->getCustomerAttributeValue($customerId, $attribute);
    }

    public function getLoggedInGroup()
    {
        return $this->getCustomerGroup($this->getLoggedInCustomerId());
    }

    public function isLoggedIn()
    {
        return $this->session && $this->session->isLoggedIn();
    }

    public function getLoggedInCustomer()
    {
        return $this->isLoggedIn() ? $this->getCustomer($this->getLoggedInCustomerId()) : null;
    }

    public function getLoggedInCustomerId()
    {
        return $this->isLoggedIn() ? $this->session->getCustomer()->getId() : 0;
    }

    private function getCustomerById($customerId = 0)
    {
        $factory = $this->generateClassObject("Magento\Customer\Model\CustomerFactory")->create();
        return $factory->load($customerId);
    }

    private function getCustomerGroup($customerId)
    {
      $customerGroup = 0;
      try {
          $customerGroup = (int)$this->helperAttribute->getCustomerEntityFieldValue($customerId, "group_id");
      } catch (\Exception $e) {
          $this->helperLog->errorLog(__METHOD__, $e->getMessage());
          $customerGroup = 0;
      } finally {
          return $customerGroup;
      }
    }
}
