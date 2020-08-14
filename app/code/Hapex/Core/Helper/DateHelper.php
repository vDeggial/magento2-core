<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DateHelper extends AbstractHelper
{
    protected $objectManager;
    protected $helperLog;
    protected $timezone;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, LogHelper $helperLog, TimezoneInterface $timezone)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $helperLog;
        $this->timezone = $timezone;
    }

    public function getCurrentDate()
    {
        return $this->timezone->date();
    }

    public function isCurrentDateWithinRange($fromDate, $toDate)
    {
        $isWithinRange = false;
        $isAfter = false;
        $isBefore = false;
        try {
            $isAfter = $this->isDateAfter($fromDate);
            $isBefore = $this->isDateBefore($toDate);
            $isWithinRange = $isAfter && $isBefore;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isWithinRange = false;
        } finally {
            return $isWithinRange;
        }
    }

    private function isDateBefore($date = null)
    {
        $currentDate = $this->getCurrentDate()->format('Y-m-d');
        return $date ? strtotime($currentDate) <= strtotime($date) : true;
    }

    private function isDateAfter($date = null)
    {
        $currentDate = $this->getCurrentDate()->format('Y-m-d');
        return $date ? strtotime($currentDate) >= strtotime($date) : true;
    }
}
