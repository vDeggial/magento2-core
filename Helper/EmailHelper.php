<?php
namespace Hapex\Core\Helper;

class EmailHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;

    protected $inlineTranslation;

    protected $logger;
    
    protected $transportBuilder;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->storeManager = $storeManager;
    }
    
    protected function send($from, $to, $templateId, $vars, $store = null, $area = \Magento\Framework\App\Area::AREA_FRONTEND) {
        try
        {
            if (!$store)
            {
                $store = $this->storeManager->getStore()->getStoreId();
            }
            
            
            $this->inlineTranslation->suspend();
            $this->transportBuilder
                    ->setTemplateIdentifier($templateId)
                    ->setTemplateOptions([
                        'area' => $area,
                        'store' => $store
                    ])
                    ->setTemplateVars($vars)
                    ->setFrom($from)
                    ->addTo($to);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            
            $this->inlineTranslation->resume();
            
            return true;
        }
        
        catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }
    }
}
