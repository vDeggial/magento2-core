<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class DateHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
      $this->objectManager = $objectManager;
      $this->helperLog = $this->generateClassObject("Hapex\Core\Helper\LogHelper");
        parent::__construct($context);

    }

    public function getCurrentDate()
    {
        $date = null;
        try {
            $timezone = $this->objectManager->get("Magento\Framework\Stdlib\DateTime\TimezoneInterface");
            $date = $timezone->date();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $date = null;
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
            $afterFromDate = $fromDate ? strtotime($currentDate) >= strtotime($fromDate) ? true : false : true;
            $beforeToDate = $toDate ? strtotime($currentDate) <= strtotime($toDate) ? true : false : true;
            $isWithinRange = $afterFromDate && $beforeToDate;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $isWithinRange = false;
        } finally {
            return $isWithinRange;
        }
    }
}
