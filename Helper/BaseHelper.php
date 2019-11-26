<?php

namespace Hapex\Core\Helper;
use Magento\Framework\App\Helper\Context;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter;

class BaseHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    
    public function getProductStockQty($productId)
    {
        $qty = 0;
        try
        {
            if ($productId)
            {
                $itemStockTable = $this->getSqlTableName('cataloginventory_stock_item');
                $productEntityTable = $this->getSqlTableName('catalog_product_entity');
                $sql = "SELECT stock.qty as qty FROM $itemStockTable stock join $productEntityTable product on stock.product_id = product.entity_id where product.entity_id = $productId";
                $result = $this->sqlQueryFetchOne($sql);
                if ($result)
                {
                    $qty = (int)$result;
                }
            }
            return $qty;
        }
        catch (\Exception $e)
        {
            return -1;
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
    
    public function sendOutput($output)
    {
        print($output);
    }
    
    protected function getCurrentDate()
    {
        $timezone = $this->generateClassObject("Magento\Framework\Stdlib\DateTime\TimezoneInterface");
        return !($timezone === null) ? $timezone->date() : null;
    }
    
    protected function getSqlTableName($name)
    {
        if ($name)
        {
            $resource = $this->getSqlResource();
            if ($resource)
            {
                $tableName = $resource->getTableName($name);
                if ($resource->getConnection()->isTableExists($tableName))
                {
                    return $tableName;
                }
            }
        }
        
        return "";
    }
    
    protected function sqlQuery($sql)
    {
        return $this->queryExecute($sql);
    }
    
    protected function sqlQueryFetchAll($sql, $limit = 0)
    {
        if ($limit > 0) $sql .= " LIMIT $limit";
        return $this->queryExecute($sql,"fetchAll");
    }
    
    protected function sqlQueryFetchOne($sql)
    {
        return $this->queryExecute($sql,"fetchOne");
    }
    
    protected function sqlQueryFetchRow($sql)
    {
        return $this->queryExecute($sql,"fetchRow");
    }
    
    protected function urlExists($remoteUrl = "")
    {
        $handle = @curl_init($remoteUrl);
        @curl_setopt($handle, CURLOPT_HEADER, TRUE);
        @curl_setopt($handle, CURLOPT_NOBODY, TRUE);
        @curl_setopt($handle, CURLOPT_FOLLOWLOCATION, FALSE);
        @curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $status = array();
        preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($handle) , $status);
        return ($status[1] == 200);
    }
    
    private function generateClassObject($class = "")
    {
        if (!empty($class))
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get($class);
        }
        
        return null;
    }
    
    private function getSqlResource()
    {
        return $this->generateClassObject("Magento\Framework\App\ResourceConnection");
    }
    
    private function queryExecute($sql, $command = null)
    {
        try
        {
            if ($sql)
            {
                $resource = $this->getSqlResource();
                $connection = $resource->getConnection();
                if ($connection)
                {
                    switch ($command)
                    {
                        case "fetchOne":
                            return $connection->fetchOne($sql);
                            break;
                        case "fetchAll":
                            return $connection->fetchAll($sql);
                            break;
                        case "fetchRow":
                            return $connection->fetchRow($sql);
                            break;
                        default:
                            return $connection->query($sql);
                            break;
                    }
                }
            }
            return null;
        }
        catch (\Exception $e)
        {
            return null;
        }
    }
}
