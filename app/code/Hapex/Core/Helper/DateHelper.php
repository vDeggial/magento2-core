<?php

namespace Hapex\Core\Helper;

use DateTime;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use stdClass;

class DateHelper extends AbstractHelper
{
    protected $objectManager;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $this->objectManager->get(LogHelper::class);
    }

    public function getCurrentDate()
    {
        $date = new DateTime();
        try {
            $timezone = $this->objectManager->get(TimezoneInterface::class);
            $date = $timezone->date();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = new DateTime();
        } finally {
            return $date;
        }
    }

    public function isCurrentDateWithinRange($fromDate, $toDate)
    {
        $isWithinRange = false;
        $afterFromDate = false;
        $beforeToDate = false;
        $currentDate = null;

        try {
            $currentDate = $this->getCurrentDate()->format('Y-m-d');
            $afterFromDate = $fromDate ? strtotime($currentDate) >= strtotime($fromDate) : true;
            $beforeToDate = $toDate ? strtotime($currentDate) <= strtotime($toDate) : true;
            $isWithinRange = $afterFromDate && $beforeToDate;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isWithinRange = false;
        } finally {
            return $isWithinRange;
        }
    }
}
