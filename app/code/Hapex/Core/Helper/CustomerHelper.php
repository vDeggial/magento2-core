<?php

namespace Hapex\Core\Helper;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CustomerHelper extends BaseHelper
{
    protected $session;
    protected $helperEav;
    public function __construct(Context $context, ObjectManagerInterface $objectManager, CustomerEavHelper $helperEav, SessionFactory $session)
    {
        parent::__construct($context, $objectManager);
        $this->helperEav = $helperEav;
        $this->session = $session->create();
    }

    public function getCustomer($customerId = 0)
    {
        return $this->getCustomerById($customerId);
    }

    public function getAttributeValue($customerId = null, $attribute = null)
    {
        return $this->helperEav->getCustomerAttributeValue($customerId, $attribute);
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
        $factory = $this->generateClassObject(CustomerFactory::class)->create();
        return $factory->load($customerId);
    }

    private function getCustomerGroup($customerId)
    {
        $customerGroup = 0;
        try {
            $customerGroup = (int) $this->helperEav->getCustomerEntityFieldValue($customerId, "group_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $customerGroup = 0;
        } finally {
            return $customerGroup;
        }
    }
}
