<?php

namespace Hapex\Core\Cron;

use Hapex\Core\Helper\DataHelper;
use Hapex\Core\Helper\LogHelper;

class BaseCron
{
    protected $helperData;
    protected $helperLog;
    protected $maintenanceMode;
    protected $isMaintenance;

    public function __construct(DataHelper $helperData, LogHelper $helperLog)
    {
        $this->helperData = $helperData;
        $this->helperLog = $helperLog;
        $this->maintenanceMode = $this->helperData->generateClassObject(\Magento\Framework\App\MaintenanceMode::class);
        $this->isMaintenance = isset($this->maintenanceMode) ? $this->maintenanceMode->isOn() : true;
    }
}
