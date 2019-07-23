<?php

namespace Hapex\Core\Helper;

class DateHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $timezone;
    
    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone) {
        parent::__construct($context);
        $this->timezone = $timezone;
    }
    
    public function getCurrentTime($format = 'Y-m-d H:i:s')
    {
        return $this->timezone->date()->format($format);
    }
}
