<?php

namespace Hapex\Core\Helper;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class UrlHelper extends AbstractHelper
{
    protected $objectManager;
    protected $helperLog;
    protected $curl;

    public function __construct(Context $context, ObjectManagerInterface $objectManager, Curl $curl, LogHelper $helperLog)
    {
        parent::__construct($context, $objectManager);
        $this->curl = $curl;
        $this->helperLog = $helperLog;
    }

    public function getRemoteContent($url = null)
    {
        return $this->get($url)->getBody();
    }

    public function sendSlackWebhook($webhookUrl = null, $message = null)
    {
        return $this->sendWebhook($webhookUrl, json_encode(["text" => $message]), "application/json");
    }

    public function sendWebhook($webhookUrl = null, $message = null, $contentType = "text/plain")
    {
        return $this->sendRemoteContent($webhookUrl, $message, $contentType);
    }

    public function sendRemoteContent($url = null, $data = null, $contentType = null)
    {
        return $this->post($url, $data, $contentType);
    }

    public function urlExists($remoteUrl = null)
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

    private function get($url = null)
    {
        try {
            $options = [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3",
            ];
            $this->curl->setOptions($options);
            $this->curl->get($url);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $this->curl;
        }
    }

    private function post($url = null, $data = null, $contentType = "application/json")
    {
        try {
            $options = [
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 3,
            ];
            $headers = ["Content-Type" => $contentType];
            $this->curl->setOptions($options);
            $this->curl->setHeaders($headers);
            $this->curl->post($url, $data);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
        } finally {
            return $this->curl;
        }
    }
}
