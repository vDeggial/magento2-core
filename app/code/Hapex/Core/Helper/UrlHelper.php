<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

class UrlHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $helperLog;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $this->objectManager->get(\Hapex\Core\Helper\LogHelper::class);
    }

    public function getRemoteContent($url)
    {
        return $this->get($url)->getBody();
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
        $curl = $this->objectManager->get(\Magento\Framework\HTTP\Client\Curl::class);
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3",
        );
        $curl->setOptions($options);
        $curl->get($url);
        return $curl;
    }
}
