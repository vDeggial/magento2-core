<?php

namespace Hapex\Core\Helper;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class UrlHelper extends AbstractHelper
{
    protected $objectManager;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, LogHelper $helperLog)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $helperLog;
    }

    public function getRemoteContent($url)
    {
        return $this->get($url)->getBody();
    }

    public function sendSlackWebhook($webhookUrl = null, $message = null)
    {
        $data = json_encode(["text" => $message]);
        return $this->sendRemoteContent($webhookUrl, $data, "application/json");
    }

    public function sendRemoteContent($url = null, $data = null, $contentType = null)
    {
        return $this->post($url, $data, $contentType);
    }

    public function urlExists($remoteUrl = "")
    {
        $exists = false;
        try {
            $exists = $this->get($remoteUrl)->getStatus() === 200;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    private function get($url = "")
    {
        $curl = $this->objectManager->get(Curl::class);
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3",
        );
        $curl->setOptions($options);
        $curl->get($url);
        return $curl;
    }

    private function post($url = null, $data = null, $contentType = "application/json")
    {
        $curl = $this->objectManager->get(Curl::class);
        $options = array(
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
        );
        $curl->setOptions($options);
        $curl->addHeader("Content-Type", $contentType);
        $curl->post($url, $data);
        return $curl;
    }
}
