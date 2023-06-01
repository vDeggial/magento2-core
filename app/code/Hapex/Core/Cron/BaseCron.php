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
        $this->maintenanceMode = isset($this->helperData) ? $this->helperData->generateClassObject(\Magento\Framework\App\MaintenanceMode::class) : null;
        $this->isMaintenance = isset($this->maintenanceMode) ? $this->maintenanceMode->isOn() : true;
    }
    
    public function getExceptionTrace($e, $seen = null): ?string
    {
        return $this->helperData->getExceptionTrace($e);
    }
}
