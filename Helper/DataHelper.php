<?php

namespace Hapex\Core\Helper;
use Magento\Framework\App\Helper\Context;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter;

class DataHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    
    public function getConfigFlag($path, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }
    
    public function getConfigValue($path, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }
    
    public function getObject($class)
    {
        if (!empty($class))
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get($class);
        }
    }
    
    public function getProductStockQty($id)
    {
        $qty = 0;
        try
        {
            if ($id)
            {
                $resource = $this->getObject("Magento\Framework\App\ResourceConnection");
                $connection = $resource->getConnection();
                if ($connection)
                {
                    $itemStockTable = $resource->getTableName('cataloginventory_stock_item');
                    $productEntityTable = $resource->getTableName('catalog_product_entity');
                    $sql = "SELECT stock.qty as qty FROM $itemStockTable stock join $productEntityTable product on stock.product_id = product.entity_id where product.entity_id = $id";
                    $result = $connection->fetchOne($sql);
                    if ($result)
                    {
                        $qty = (int)$result;
                    }
                }
            }
            return $qty;
        }
        catch (\Exception $e)
        {
            return 0;
        }
    }
    
     public function printLog($filename,$log)
    {
       $writer = new Stream(BP . "/var/log/$filename.log");
       $logger = new Logger();
       $formatter = new Formatter\Simple();
       $formatter->setDateTimeFormat("Y-m-d H:i:s T");
       $writer->setFormatter($formatter);
       $logger->addWriter($writer);
       $logger->info($log);
    }

}
