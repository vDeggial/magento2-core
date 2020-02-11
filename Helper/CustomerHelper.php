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

    public function getLoggedInGroup()
    {
        return $this->isLoggedIn() ? $this->session->getCustomer()->getGroupId() : 0;
    }

    public function isLoggedIn()
    {
        return $this->session && $this->session->isLoggedIn();
    }
}
