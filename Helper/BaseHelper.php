<?php

namespace Hapex\Core\Helper;
use Magento\Framework\App\Helper\Context;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter;

class BaseHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getProductStockQty($productId)
    {
        try
        {
            $itemStockTable = $this->getSqlTableName('cataloginventory_stock_item');
            $productEntityTable = $this->getSqlTableName('catalog_product_entity');
            $sql = $itemStockTable && $productEntityTable && $productId ? "SELECT stock.qty as qty FROM $itemStockTable stock join $productEntityTable product on stock.product_id = product.entity_id where product.entity_id = $productId" : null;
            $result = $sql ? $this->sqlQueryFetchOne($sql) : null;
                
            return $result ? (int)$result : 0;
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
    
    protected function generateClassObject($class = "")
    {
        $objectManager = !empty($class) ? \Magento\Framework\App\ObjectManager::getInstance() : null;
        return $objectManager ? $objectManager->get($class): null;
    }
    
    protected function getCurrentDate()
    {
        $timezone = $this->generateClassObject("Magento\Framework\Stdlib\DateTime\TimezoneInterface");
        return !($timezone === null) ? $timezone->date() : null;
    }
    
    protected function getSqlTableName($name = null)
    {
        $resource = $name ? $this->getSqlResource() : null;
        $tableName = $resource ? $resource->getTableName($name) : null;
        
        return $tableName && $resource->getConnection()->isTableExists($tableName) ? $tableName : null;
    }
    
    public function isCurrentDateWithinRange($fromDate, $toDate)
    {
        $afterFromDate = false;
        $beforeToDate = false;

        $currentDate = $this->getCurrentDate()->format('Y-m-d');
    
        $afterFromDate = $fromDate ? strtotime($currentDate) >= strtotime($fromDate) ? true : false : true;
        
        $beforeToDate = $toDate ? strtotime($currentDate) <= strtotime($toDate) ? true : false : true;
                
        return $afterFromDate && $beforeToDate;
    }
    
    protected function sqlQuery($sql)
    {
        return $this->queryExecute($sql);
    }
    
    protected function sqlQueryFetchAll($sql, $limit = 0)
    {
        $sql .= ($limit > 0) ? " LIMIT $limit" : "";
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
    
    private function getSqlResource()
    {
        return $this->generateClassObject("Magento\Framework\App\ResourceConnection");
    }
    
    private function queryExecute($sql = null, $command = null)
    {
        try
        {
            $resource = $sql ? $this->getSqlResource(): null;
            $connection = $resource ? $resource->getConnection() : null;
            $result = null;
            
            switch($connection !== null)
            {
                case true:
                    switch ($command)
                    {
                        case "fetchOne":
                            $result =  $connection->fetchOne($sql);
                            break;
                        case "fetchAll":
                            $result = $connection->fetchAll($sql);
                            break;
                        case "fetchRow":
                            $result = $connection->fetchRow($sql);
                            break;
                        default:
                            $result = $connection->query($sql);
                            break;
                    }
                    break;
            }
            return $result;
        }
        catch (\Exception $e)
        {
            return null;
        }
    }
}
