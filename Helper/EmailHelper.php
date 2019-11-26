<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;

class EmailHelper extends BaseHelper
{
    protected $scopeConfig;

    protected $inlineTranslation;

    protected $logger;
    
    protected $escaper;

    protected $transportBuilder;

    protected $storeManager;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->storeManager = $storeManager;
    }
    
    protected function send($sender, $receiver, $templateId, $vars, $store = null, $area = \Magento\Framework\App\Area::AREA_FRONTEND) {
        try
        {
            $store = !$store ? $this->storeManager->getStore()->getStoreId() : $store;

            $this->inlineTranslation->suspend();
            $this->transportBuilder
                    ->setTemplateIdentifier($templateId)
                    ->setTemplateOptions([
                        'area' => $area,
                        'store' => $store
                    ])
                    ->setTemplateVars($vars)
                    ->setFrom($sender)
                    ->addTo($receiver);
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
