<?php

namespace Hapex\Core\Observer;

use Hapex\Core\Helper\LogHelper;
use Hapex\Core\Helper\DataHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class BaseObserver implements ObserverInterface
{

    protected $helperLog;
    protected $helperData;
    protected $messageManager;
    protected $event;

    public function __construct(
        DataHelper $helperData,
        LogHelper $helperLog,
        ManagerInterface $messageManager,
    ) {
        $this->helperData = $helperData;
        $this->helperLog = $helperLog;
        $this->messageManager = $messageManager;
        $this->event = $this->helperData->generateClassObject(DataObject::class);
    }

    public function execute(Observer $observer)
    {
        try {
            $this->event = $this->getEvent($observer);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $this->messageManager->addErrorMessage($e->getMessage());
        } finally {
            return $this;
        }
    }

    public function getExceptionTrace($e, $seen = null): ?string
    {
        return $this->helperData->getExceptionTrace($e);
    }

    protected function getEvent($observer = null)
    {
        return $observer ? $observer->getEvent() : null;
    }
}
