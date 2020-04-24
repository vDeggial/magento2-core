<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class CustomerHelper extends BaseHelper
{
    protected $session;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->session = $this->generateClassObject('Magento\Customer\Model\SessionFactory')->create();
    }

    public function getCustomerById($customerId = 0)
    {
        $factory = $this->generateClassObject("Magento\Customer\Model\CustomerFactory")->create();
        return $factory->load($customerId);
    }

    public function getCustomAttributeValue($customer = null, $attribute = null)
    {
        $value = null;
        try {
            $repository = $this->generateClassObject("Magento\Customer\Api\CustomerRepositoryInterface");
            $customer = $repository->getById($customer->getId());
            $attribute = $customer->getCustomAttribute($attribute);
            $value = $attribute !== null ? $attribute->getValue() : false;
        } catch (\Exception $e) {
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getLoggedInGroup()
    {
        $customer = $this->getLoggedInCustomer();
        return $customer != null ? $customer->getGroupId() : 0;
    }

    public function isLoggedIn()
    {
        return $this->session && $this->session->isLoggedIn();
    }

    public function getLoggedInCustomer()
    {
        return $this->isLoggedIn() ? $this->session->getCustomer() : null;
    }
}
