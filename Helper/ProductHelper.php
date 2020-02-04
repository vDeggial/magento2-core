<?php

namespace Hapex\Core\Helper;
use Magento\Framework\App\Helper\Context;

class ProductHelper extends BaseHelper
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

}
