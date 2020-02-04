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

    protected function getProduct($productId)
    {
        $productFactory = $this->generateClassObject("Magento\Catalog\Model\ProductFactory");
        return $this->productExists($productId) ? $productFactory->create()->load($productId) : null;
    }

    protected function getProductImages($product, $maxSize = "500")
    {
        switch($product !== null)
        {
            case true:
                $imageList = [];
                $images = $product->getMediaGalleryImages();
                $_imageHelper = $this->generateClassObject('Magento\Catalog\Helper\Image');
                foreach($images as $image)
                {
                    array_push($imageList, $_imageHelper !== null ? $_imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($image->getFile())->resize($maxSize,null)->getUrl() : "");
                }
                return $imageList;
            default:
                return [];
        }
    }

    protected function productExists($productId)
    {
        $productEntityTable = $this->getSqlTableName('catalog_product_entity');
        $sql = $productEntityTable && $productId ? "SELECT * FROM $productEntityTable product where product.entity_id = $productId" : null;
        $result = $sql ? $this->sqlQueryFetchOne($sql) : null;
        return $result && !empty($result);
    }

}
