<?php

namespace Hapex\Core\Cron;

use Hapex\Core\Helper\DataHelper;
use Hapex\Core\Helper\LogHelper;

class BaseCron
{
    protected $helperData;
    protected $helperLog;

    public function __construct(DataHelper $helperData, LogHelper $helperLog)
    {
        $this->helperData = $helperData;
        $this->helperLog = $helperLog;
    }
}
