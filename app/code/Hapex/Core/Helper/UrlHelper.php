<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class UrlHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $helperLog;


    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->helperLog = $this->objectManager->get("Hapex\Core\Helper\LogHelper");
    }

    public function getRemoteContent($url)
    {
        $curl = $this->objectManager->get("Magento\Framework\HTTP\Client\Curl");
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3",
    );
        $curl->setOptions($options);
        $curl->get($url);
        $htmlContent = $curl->getBody();
        return $htmlContent;
    }

    public function urlExists($remoteUrl = "")
    {
        $exists = false;
        try {
            $curl = $this->objectManager->get("Magento\Framework\HTTP\Client\Curl");
            $curl->get($remoteUrl);
            $exists = $curl->getStatus() === 200;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }
}
