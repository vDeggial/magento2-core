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
    
    public function displayPackCardsLeftMessage($product, $ticker = false)
    {
        if ($product)
        {
            $packType = $product->getAttributeText('pack_type');
            $prefix = "";
            switch ($packType)
            {
                case "Equity":
                    $prefix = "SHARES";
                    break;
                case "Randomized":
                    $prefix = "CARDS";
                    break;
                case "Box Break":
                    $prefix = "PACKS";
                    break;
                case "Set Break":
                case "Rip Random":
                    $prefix = "SPOTS";
                    break;
            }
                        
            $isPack = $prefix != "";
            if ($isPack)
            {
                $qty = $this->getProductStockQty($product->getId());
                if (!$ticker)
                {
                    $sharePercent = $product->getData('share_percent');
                    $sharePercentMessage = $sharePercent != "" ? "EACH SHARE IS $sharePercent%" : "";
                    $message = $qty > 0 ? "$prefix LEFT: $qty" : "SOLD OUT";
                    $message = "<div class = \"product attribute cards\">$message";
                    if ($packType == "Equity" && $sharePercent != "") $message .= "<div class = \"percent\">$sharePercentMessage</div>";
                    $message .= "</div>";
                    echo $message;
                }
                else
                {
                    $message = $qty > 0 ? "($qty " . $prefix . " LEFT)" : " (SOLD OUT)";
                    echo $isPack ? " <b>$message</b>" : "";
                }
            }
        }
    }
    
    public function displayPackProductTags($product)
    {
        if ($product)
        {
            $tags = [];
            $tags[] = ["name" => "fan_favorite", "color" => "#002684", "text" => "FAN FAVORITE"];
            $tags[] = ["name" => "most_valuable", "color" => "darkgreen", "text" => "MOST VALUABLE"];
            $tags[] = ["name" => "charity_pack", "color" => "red", "text" => "CHARITY PACK"];
            $tags[] = ["name" => "charlies_pick", "color" => "orange", "text" => "CHARLIE'S PICK"];
            $tags[] = ["name" => "extremely_rare", "color" => "purple", "text" => "EXTREMELY RARE"];
            $tags[] = ["name" => "halloween_pack", "color" => "#ff6600", "text" => "HALLOWEEN PACK"];
                                
            foreach ($tags as $tag)
            {
                if ($product->getData($tag["name"]))
                {
                    $color = $tag["color"];
                    $text = $tag["text"];
                    echo "<div style = \"display:inline-flex\"><span class = \"pack-tag\" style=\"color: $color;\"><i class=\"fa fa-star\" aria-hidden=\"true\"></i> <span>$text</span> <i class=\"fa fa-star\" aria-hidden=\"true\"></i></span></div>";
                }
            }
        }
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
